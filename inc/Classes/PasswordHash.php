<?php

namespace LanSuite;

class PasswordHash
{
    public static function hash(string $password,array $algo_cfg = null)
    {
        //use provided config or default
        if ($algo_cfg == null) {
            $algo_cfg = self::getAlgoCfg();
        }
        switch ($algo_cfg['algo']) {
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
            if (self::isAlgorithmSupported($algo_cfg['algo'])) {

                $iterations = intval($algo_cfg['iterations']);
                $algo = str_replace('pbkdf2-', '', $algo_cfg['algo']);

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
                throw new \Exception('Unsupported hash algorithm configured: ' . $algo_cfg['algo']);
            }
        }
    }

    public static function verify($password, $hash)
    {
        $info = self::getInfo($hash);

        switch ($info['algo']) {
        case 'md5':
            $newhash = md5($password);
            break;
        case 'md5-sha512':
            $newhash = hash_pbkdf2('sha512', md5($password), $info['salt'], intval($info['iterations']), 0, true);
            break;
        default:
            if (self::isAlgorithmSupported($info['algo'])) {
                $algo = str_replace('pbkdf2-', '', $info['algo']);
                $newhash = hash_pbkdf2($algo, $password, $info['salt'], intval($info['iterations']), 0, true);
            } else {
                throw new \Exception('Unsupported hash algorithm configured: ' . $algo_cfg['algo']);
            }
        }
        return hash_equals($info['hash'], $newhash);
    }

    public static function needsRehash($hash)
    {
        global $cfg;

        $algo_cfg = self::getAlgoCfg();

        $info = self::getInfo($hash);

        foreach ($info as $key => $value) {
            if ($key === 'hash' || $key === 'salt') {
                continue;
            }

            if ($algo_cfg[$key] !== $value) {
                return true;
            }
        }

        return false;
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

            return array('algo' => $algo, 'iterations' => $iterations, 'hash' => $rawhash, 'salt' => $rawsalt);
        } else {
            return array('algo' => 'md5', 'iterations' => 0, 'hash' => $hash, 'salt' => '');
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

    private static function getDefaultAlgoCfg($algo)
    {
        switch ($algo) {
            default:
                return array('iterations' => '500000');
        }

        return array();
    }


    /**
     * It appears that custom config was supposed to be loaded from an imploded string to be stored in the config, but
     *
     */
    private static function parseAlgoCfg($algo_cfg_str)
    {
        $algo_cfg = array();

        $pairs = explode(',', $algo_cfg_str);

        foreach ($pairs as $pair) {
            $parts = explode('=', $pair, 2);

            $key = $parts[0];

            if (count($parts) > 1) {
                $value = $parts[1];
            } else {
                $value = null;
            }

            $algo_cfg[$key] = $value;
        }

        return $algo_cfg;
    }

    private static function getAlgoCfg()
    {
        global $cfg;

        $algo = self::getAlgo();
        $algo_cfg = self::getDefaultAlgoCfg($algo);

        if (array_key_exists('password_hash_algorithm', $cfg)) {
            $custom_algo_cfg = self::parseAlgoCfg($cfg['password_hash_algorithm']);
            $algo_cfg = array_merge($algo_cfg, $custom_algo_cfg);
        }

        return array_merge($algo_cfg, array('algo' => $algo));
    }

    /**
     * Checks if a given Hash-Algorithm is available on the system
     *
     * @param string $hashAlgorithm
     * @return bool true if algorithm is available, false if not
     */
    private static function isAlgorithmSupported(string $hashAlgorithm) {
        //remove unnecessary 'pbkdf2-' if present to match built-in names
        $hashAlgorithm = str_replace('pbkdf2-', '', $hashAlgorithm);
        //return if in list of supported algorithms
        return in_array($hashAlgorithm, hash_algos());
    }
}
