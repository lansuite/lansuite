<?php

namespace LanSuite;

class PasswordHash
{
    public static function hash($password): string
    {
        $algo_cfg = self::getAlgoCfg();

        switch ($algo_cfg['algo']) {
            case 'md5':
                return md5(string: $password);

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
                if (!is_numeric(value: $iterations) || $iterations < 1) {
                    throw new \Exception(message: 'Unexpected iterations value');
                }
                $iterations = intval($iterations);

                $rawsalt = random_bytes(length: 16);
                $rawhash = hash_pbkdf2(algo: 'sha1', password: $password, salt: $rawsalt, iterations: $iterations, length: 0, binary: true);

                $b64salt = base64_encode(string: $rawsalt);
                $b64hash = base64_encode(string: $rawhash);
                if ($b64salt === false || $b64hash === false) {
                    throw new \Exception(message: 'Unexpected base64_encode error');
                }

                return '$pbkdf2-sha1$' . $iterations . '$' . $b64salt . '$' . $b64hash;

            case 'argon2id':
                $options = self::getArgon2idOptions(algo_cfg: $algo_cfg);
                return password_hash(password: $password, algo: PASSWORD_ARGON2ID, options: $options);
        }

        throw new \Exception(message: 'Unsupported password hashing algorithm');
    }

    public static function verify($password, $hash): bool
    {
        $info = self::getInfo(hash: $hash);

        switch ($info['algo']) {
            case 'md5':
                $newhash = md5(string: $password);
                return hash_equals(known_string: $info['hash'], user_string: $newhash);
            case 'pbkdf2-sha1':
                $newhash = hash_pbkdf2(algo: 'sha1', password: $password, salt: $info['salt'], iterations: intval(value: $info['iterations']), length: 0, binary: true);
                return hash_equals(known_string: $info['hash'], user_string: $newhash);
            case 'argon2id':
                return password_verify(password: $password, hash: $hash);
        }

        return false;
    }

    public static function needsRehash($hash): bool
    {
        global $config;

        $algo_cfg = self::getAlgoCfg();

        $info = self::getInfo(hash: $hash);
        if ($info['algo'] !== self::getAlgo()) {
            return true;
        }
        if ($info['algo'] === 'pbkdf2-sha1') {
            foreach ($info as $key => $value) {
                if ($key === 'hash' || $key === 'salt') {
                    continue;
                }

                if ($algo_cfg[$key] !== $value) {
                    return true;
                }
            }
        } elseif ($info['algo'] === 'argon2id') {
            $options = self::getArgon2idOptions(algo_cfg: $algo_cfg);
            return password_needs_rehash(hash: $hash, algo: PASSWORD_ARGON2ID, options: $options);
        }

        return false;
    }

    public static function getAlgoToolTip(): string
    {
        $algo_cfg = self::getAlgoCfg();
        switch ($algo_cfg['algo']) {
            case 'pbkdf2-sha1':
                return 'Define the iterations of the pbkdf2 Algorithm &#013; e.g. iterations=500000';
            case 'argon2id':
                return "Define the parameters of the Argon2ID Algorithm &#013; e.g. time_cost=3,threads=2,memory_cost=131072 &#013; you need only define those values you wan't to override";
        }
        return null;
    }

    private static function getArgon2idOptions($algo_cfg): array
    {
        $timecost = $algo_cfg['time_cost'];
        if (!is_numeric(value: $timecost) || $timecost < 1) {
            throw new \Exception(message: 'Unexpected time_cost value');
        }
        $threads = $algo_cfg['threads'];
        if (!is_numeric(value: $threads) || $threads < 1) {
            throw new \Exception(message: 'Unexpected threads value');
        }
        $memoryCost = $algo_cfg['memory_cost'];
        if (!is_numeric(value: $memoryCost) || $memoryCost < 8) {
            throw new \Exception(message: 'Unexpected memory_cost value');
        }
        $options = ['time_cost' => $timecost, 'threads' => $threads, 'memory_cost' => $memoryCost];
        return $options;
    }

    private static function getInfo($hash): array
    {
        if ($hash[0] === '$') {
            $parts = explode(separator: '$', string: $hash);
            if (count(value: $parts) === 5) {
                list($dummy, $algo, $iterations, $b64salt, $b64hash) = $parts;
                if ($algo === 'pbkdf2-sha1') {
                    return self::decodepbkdf2(hash: $hash);
                } else {
                    throw new \Exception(message: 'Unexpected hash format');
                }
            } elseif (count(value: $parts) === 6) {
                list($dummy, $algo, $version, $options, $b64salt, $b64hash) = $parts;
                if ($algo === 'argon2id') {
                    return password_get_info(hash: $hash);
                } else {
                    throw new \Exception(message: 'Unexpected hash format');
                }
            } else {
                throw new \Exception(message: 'Unexpected hash format');
            }
        } else {
            return array('algo' => 'md5', 'hash' => $hash);
        }
    }

    private static function decodepbkdf2($hash): array
    {
        $parts = explode(separator: '$', string: $hash);
        if (count(value: $parts) !== 5) {
            throw new \Exception(message: 'Unexpected hash format');
        }
        list($dummy, $algo, $iterations, $b64salt, $b64hash) = $parts;

        if (!is_numeric(value: $iterations) || $iterations < 1) {
            throw new \Exception(message: 'Unexpected iterations value');
        }
        $rawsalt = base64_decode(string: $b64salt, strict: true);
        $rawhash = base64_decode(string: $b64hash, strict: true);
        if ($rawsalt === false || $rawhash === false) {
            throw new \Exception(message: 'Unexpected base64_decode error');
        }

        return array('algo' => $algo, 'iterations' => $iterations, 'hash' => $rawhash, 'salt' => $rawsalt);
    }

    private static function getAlgo(): string
    {
        global $cfg;

        if (array_key_exists(key: 'pwhash_algo', array: $cfg)) {
            $pwhash_algo = $cfg['pwhash_algo'];
        } else {
            $pwhash_algo = 'md5';
        }

        if ($pwhash_algo === 'default') {
            $pwhash_algo = 'argon2id';
        }

        return $pwhash_algo;
    }

    private static function getDefaultAlgoCfg($algo): array
    {
        switch ($algo) {
            case 'pbkdf2-sha1':
                return array('iterations' => '500000');
            case 'argon2id':
                return array('time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST, 'threads' => PASSWORD_ARGON2_DEFAULT_THREADS, 'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST);
        }

        return array();
    }

    private static function parseAlgoCfg($algo_cfg_str): array
    {
        $algo_cfg = array();

        $pairs = explode(separator: ',', string: $algo_cfg_str);

        foreach ($pairs as $pair) {
            $parts = explode(separator: '=', string: $pair, limit: 2);

            $key = $parts[0];

            if (count(value: $parts) > 1) {
                $value = $parts[1];
            } else {
                $value = null;
            }

            $algo_cfg[$key] = $value;
        }

        return $algo_cfg;
    }

    private static function getAlgoCfg(): array
    {
        global $cfg;

        $algo = self::getAlgo();
        $algo_cfg = self::getDefaultAlgoCfg(algo: $algo);

        if (array_key_exists(key: 'pwhash_algo_cfg', array: $cfg)) {
            $custom_algo_cfg = self::parseAlgoCfg(algo_cfg_str: $cfg['pwhash_algo_cfg']);
            $algo_cfg = array_merge($algo_cfg, $custom_algo_cfg);
        }

        return array_merge($algo_cfg, array('algo' => $algo));
    }
}
