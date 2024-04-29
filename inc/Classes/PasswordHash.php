<?php

namespace LanSuite;

class PasswordHash
{
    //define default values to be used
    public final const PBKDF2_DEFAULT_CONFIG =
    [
        'default' => ['algorithm'=>'sha512', 'iterations' => 50000],
        'md5-sha512' => ['algorithm'=>'sha512', 'iterations' => 50000],
        'pbkdf2-sha1' => ['algorithm'=>'sha1', 'iterations' => 200000],
        'pbkdf2-sha256' => ['algorithm'=>'sha256', 'iterations' => 50000],
        'pbkdf2-sha512' => ['algorithm'=>'sha512', 'iterations' => 50000]
    ];


    /**
     * creates a hash from a given password with either system default config or the configuration provided
     *
     * @param string $password The password to be hashed as cleartext
     * @param array $algo_cfg A custom configuration to be used if provided
     * @return string The hashed ciphertext with additional header information
     */
    public static function hash(string $password, array $algo_cfg = null) :string
    {
        //use provided config or default
        if ($algo_cfg == null) {
            $algo_cfg = self::getAlgoCfg();
        }
        switch ($algo_cfg['algorithm']) {
        case 'md5':
            return md5($password);
        case 'md5-sha512':
            $iterations = intval($algo_cfg['iterations']);
            $rawsalt = random_bytes(16);
            $rawhash = hash_pbkdf2($algo, $password, $rawsalt, $iterations, 0, true);
            $b64salt = base64_encode($rawsalt);
            $b64hash = base64_encode($rawhash);
            if ($b64salt === false || $b64hash === false) {
                throw new \Exception('Unexpected base64_encode error');
            }
            return '$md5-sha512' . '$'.$iterations.'$'.$b64salt.'$'.$b64hash;
        default:
            /*
            * Hash format: $pbkdf2-(algorithm)$(iterations)$(salt)$(hash)
            * Example:     $pbkdf2-sha1$500000$o2ermOW/WQy1XFFDVfx/Zw==$otf1NOkfKFTrIh9Au1oTPdwdnTc=
            * Parameters:
            *   - iterations: integer
            *   - salt: base64-encoded salt
            *   - hash: base64-encoded hash
            */

            //check that selected algo is available
            if (self::isAlgorithmSupported($algo_cfg['algorithm'])) {

                $iterations = intval($algo_cfg['iterations']);
                $algo = str_replace('pbkdf2-', '', $algo_cfg['algorithm']);

                //check that a solid number of iterations is configured
                if (!is_numeric($iterations) || $iterations < 1) {
                        throw new \Exception('Unexpected iterations value');
                    }
                    $rawsalt = random_bytes(16);
                    $rawhash = hash_pbkdf2($algo, $password, $rawsalt, $iterations, 0, true);

                    $b64salt = base64_encode($rawsalt);
                    $b64hash = base64_encode($rawhash);
                    if ($b64salt === false || $b64hash === false) {
                        throw new \Exception('Unexpected base64_encode error');
                    }

                    return '$pbkdf2-' . $algo . '$'.$iterations.'$'.$b64salt.'$'.$b64hash;
            }   else {
                throw new \Exception('Unsupported hash algorithm configured: ' . $algo_cfg['algorithm']);
            }
        }
    }

    public static function verify($password, $hash)
    {
        $info = self::getInfo($hash);

        switch ($info['algorithm']) {
        case 'md5':
            $newhash = md5($password);
            break;
        case 'md5-sha512':
            $newhash = hash_pbkdf2('sha512', md5($password), $info['salt'], intval($info['iterations']), 0, true);
            break;
        default:
            if (self::isAlgorithmSupported($info['algorithm'])) {
                $algo = str_replace('pbkdf2-', '', $info['algorithm']);
                $newhash = hash_pbkdf2($algo, $password, $info['salt'], intval($info['iterations']), 0, true);
            } else {
                throw new \Exception('Unsupported hash algorithm configured: ' . $algo_cfg['algorithm']);
            }
        }
        return hash_equals($info['hash'], $newhash);
    }

    public static function needsRehash($hash)
    {
        global $cfg;

        $systemConfig = self::getAlgoCfg();

        $hashProperties = self::getInfo($hash);
        // rehashing needed if algorithm does not match or amount of iterations is different than system setting
        // I was thinking about keeping higher iteration counts, but this would prevent any recovery from setting value too high by mistake
        return ($hashProperties['hash'] != $systemConfig['hash']) || ($hashProperties['iterations'] != $systemConfig['iterations']);
    }

    private static function getInfo($hash)
    {
        if ($hash[0] === '$') {
            $parts = explode('$', $hash);
            if (count($parts) !== 5) {
                throw new Exception('Unexpected hash format');
            }
            list($dummy, $algo, $iterations, $b64salt, $b64hash) = $parts;

            if (!is_numeric($iterations) || $iterations < 1) {
                throw new Exception('Unexpected iterations value');
            }
            $rawsalt = base64_decode($b64salt, true);
            $rawhash = base64_decode($b64hash, true);
            if ($rawsalt === false || $rawhash === false) {
                throw new Exception('Unexpected base64_decode error');
            }

            return array('algorithm' => $algo, 'iterations' => $iterations, 'hash' => $rawhash, 'salt' => $rawsalt);
        } else {
            return array('algorithm' => 'md5', 'iterations' => 0, 'hash' => $hash, 'salt' => '');
        }
    }

    private static function getAlgo()
    {
        global $cfg;

        if (array_key_exists('password_hash_algorithm', $cfg)) {
            $pwhash_algo = $cfg['password_hash_algorithm'];
        } else {
            $pwhash_algo = 'md5';
        }

        if ($pwhash_algo === 'default') {
            $pwhash_algo = 'pbkdf2-sha512';
        }

        return $pwhash_algo;
    }

    /**
     * Returns the default config for the algorithm provided
     * Also returns a default config if selected algorithm has no config
     * If selected algorithm is not supported on the system it is being overwritten with standard config
     * @param string $algo The algorithm to receive the config for
     * @return mixed[] Array of configuration options, currently only algorithm and iterations
     */
    public static function getDefaultAlgorithmConfig(string $algo = 'default') : array
    {
        if (PasswordHash::isAlgorithmSupported($algo)) {
            // only consider Algorithm further if supported on the system
            if (array_key_exists($algo, PasswordHash::PBKDF2_DEFAULT_CONFIG)){
                // we have a valid config, return it
                return PasswordHash::PBKDF2_DEFAULT_CONFIG[$algo];
            } else {
                //while we have no standard config, it is a supported algorithm, so we return it with the default options
                $hashConfig = PasswordHash::PBKDF2_DEFAULT_CONFIG['default'];
                $hashConfig ['algorithm'] = str_replace('pbkdf2-', '', $algo);
                return $hashConfig;
            }
        }
        //fallback to standard config if selection not supported on the system
        return PasswordHash::PBKDF2_DEFAULT_CONFIG['default'];
    }

    private static function getAlgoCfg()
    {
        global $cfg;

        $algo = self::getAlgo();
        if (array_key_exists('password_hash_algorithm', $cfg)) {
            return self::getDefaultAlgorithmConfig($cfg['password_hash_algorithm']);
        } else {
            return self::getDefaultAlgorithmConfig('default');
        }
    }

    /**
     * Checks if a given Hash-Algorithm is available on the system
     *
     * @param string $hashAlgorithm
     * @return bool true if algorithm is available, false if not
     */
    public static function isAlgorithmSupported(string $hashAlgorithm) {
        //remove unnecessary 'pbkdf2-' if present to match built-in names
        $hashAlgorithm = str_replace('pbkdf2-', '', $hashAlgorithm);
        //return if in list of supported algorithms
        return in_array($hashAlgorithm, hash_algos()) || $hashAlgorithm == 'md5-sha512';
    }
}
