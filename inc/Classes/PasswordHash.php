<?php

namespace LanSuite;

class PasswordHash
{
    public static function hash($password)
    {
        $algo_cfg = self::getAlgoCfg();

        switch ($algo_cfg['algo']) {
            case 'md5':
                return md5($password);

            case 'pbkdf2-sha1':
                /*
                 * Hash format: $pbkdf2-sha1$(iterations)$(salt)$(hash)
                 * Example:     $pbkdf2-sha1$500000$o2ermOW/WQy1XFFDVfx/Zw==$otf1NOkfKFTrIh9Au1oTPdwdnTc=
                 * Parameters:
                 *   - iterations: integer
                 *   - salt: base64-encoded salt
                 *   - hash: base64-encoded hash
                 */

                $iterations = $algo_cfg['iterations'];
                if (!is_numeric($iterations) || $iterations < 1) {
                    throw new Exception('Unexpected iterations value');
                }
                $iterations = intval($iterations);

                $rawsalt = random_bytes(16);
                $rawhash = hash_pbkdf2('sha1', $password, $rawsalt, $iterations, 0, true);

                $b64salt = base64_encode($rawsalt);
                $b64hash = base64_encode($rawhash);
                if ($b64salt === false || $b64hash === false) {
                    throw new Exception('Unexpected base64_encode error');
                }

                return '$pbkdf2-sha1$'.$iterations.'$'.$b64salt.'$'.$b64hash;
            
            case 'argon2id':
                $options = self::getargon2idoptions($algo_cfg);
                return password_hash($password,PASSWORD_ARGON2ID,$options);

        }

        throw new Exception('Unsupported password hashing algorithm');
    }

    public static function verify($password, $hash)
    {
        $info = self::getInfo($hash);

        switch ($info['algo']) {
            case 'md5':
                $newhash = md5($password);
                return hash_equals($info['hash'], $newhash);
            case 'pbkdf2-sha1':
                $newhash = hash_pbkdf2('sha1', $password, $info['salt'], intval($info['iterations']), 0, true);
                return hash_equals($info['hash'], $newhash);
            case 'argon2id':
                return password_verify($password, $hash);
        }

        return false;
    }

    public static function needsRehash($hash)
    {
        global $config;

        $algo_cfg = self::getAlgoCfg();

        $info = self::getInfo($hash);
        if($info['algo'] !== self::getAlgo()) {
            return true;
        }
        if($info['algo'] === 'pbkdf2-sha1'){
            foreach ($info as $key => $value) {
                if ($key === 'hash' || $key === 'salt') {
                    continue;
                }
    
                if ($algo_cfg[$key] !== $value) {
                    return true;
                }
            }
        } elseif($info['algo'] === 'argon2id') {
                $options = self::getargon2idoptions($algo_cfg);
                return password_needs_rehash($hash,PASSWORD_ARGON2ID,$options);
            
        }
        

        return false;
    }

    private static function getargon2idoptions($algo_cfg) {

            $timecost = $algo_cfg['time_cost'];
            if (!is_numeric($timecost) || $timecost < 1) {
                throw new Exception('Unexpected time_cost value');
            }
            $threads = $algo_cfg['threads'];
            if (!is_numeric($threads) || $threads < 1) {
                throw new Exception('Unexpected threads value');
            }
            $memorycost = $algo_cfg['memory_cost'];
            if (!is_numeric($memorycost) || $memorycost < 1) {
                throw new Exception('Unexpected memory_cost value');
            }
            $options = [ 'time_cost' => $timecost, 'threads' => $threads, 'memory_cost' => $memorycost ];
            return $options;
    }

    private static function getInfo($hash)
    {
        if ($hash[0] === '$') {
            $parts = explode('$', $hash);
            if (count($parts) === 5) {
                list($dummy, $algo, $iterations, $b64salt, $b64hash) = $parts;
                if ($algo === 'pbkdf2-sha1') {
                    return self::decodepbkdf2($hash);
                } else {
                    throw new Exception('Unexpected hash format');
                }
            } elseif (count($parts) === 6)
            {
                list($dummy, $algo, $version, $options, $b64salt, $b64hash) = $parts;
                if($algo === 'argon2id'){
                    return password_get_info($hash);
                } else {
                    throw new Exception('Unexpected hash format');
                }
            }
            else {
                throw new Exception('Unexpected hash format');
            }
        } else {
            return array('algo' => 'md5', 'hash' => $hash);
        }
    }
    
    private static function decodepbkdf2($hash)
    {
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
    }

    private static function getAlgo()
    {
        global $cfg;

        if (array_key_exists('pwhash_algo', $cfg)) {
            $pwhash_algo = $cfg['pwhash_algo'];
        } else {
            $pwhash_algo = 'md5';
        }

        if ($pwhash_algo === 'default') {
            $pwhash_algo = 'argon2id';
        }

        return $pwhash_algo;
    }

    private static function getDefaultAlgoCfg($algo)
    {
        switch ($algo) {
            case 'pbkdf2-sha1':
                return array('iterations' => '500000');
            case 'argon2id':
                return array( 'time_cost' => 3, 'threads' => 2, 'memory_cost' => 131072 );
        }

        return array();
    }

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

        if (array_key_exists('pwhash_algo_cfg', $cfg)) {
            $custom_algo_cfg = self::parseAlgoCfg($cfg['pwhash_algo_cfg']);
            $algo_cfg = array_merge($algo_cfg, $custom_algo_cfg);
        }

        return array_merge($algo_cfg, array('algo' => $algo));
    }
}
