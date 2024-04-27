<?php

namespace LanSuite\Tests;

use LanSuite\PasswordHash;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function PHPUnit\Framework\assertEquals;

class HashTest extends TestCase
{

    protected function setUp() : void
    {
        $GLOBALS['cfg'] = [];
          error_reporting(E_ALL);

        // the following test hashes will be used for all functions and tests, thus defined once and shared globally
        $GLOBALS['hashTestArray'] =
            [
                [0,'md5','098f6bcd4621d373cade4e832627b4f6'],
                [50000,'md5-sha512','$md5-sha512$50000$SX9kiDHaUHqwILCFA1RI7Q==$bo6tl4q5J8VkGna/mdb+hDpRLy0wRY2MqNe8zIYWfynke8Ke8l+uu/w4hLQEpsKBUlcQ0rgXjOJWITwcMc0zIw=='],
                [200000,'md5-sha512','$md5-sha512$200000$okqHCpO4yQwO8qd7b4RXnA==$QK4iwccZLOEe1+IqM6w8GqhyZaKy1T7vkT6FB6y/SNhE446UkhfsPLdywLYP8GxVhgCe+MV+o+ibEx3L0cpnAg=='],
                [50000,'pbkdf2-sha1','$pbkdf2-sha1$50000$LbD2N50f7pkUgue/SkDuqw==$6WkVQSh6359CrSOPEf+ItEEn+j8='],
                [200000,'pbkdf2-sha1','$pbkdf2-sha1$200000$liprMJBLrV9t3gl+T/9Ktg==$KoKX1VwaODZfab93SiOPZSs3Isg='],
                [50000,'pbkdf2-sha256','$pbkdf2-sha256$50000$y8oN21k4CgAwJXkA50SDNQ==$JqlNk+msf3kWV8x6UwYBNEgIjTOUfyJKOHC/B452+oU='],
                [50000,'pbkdf2-sha512','$pbkdf2-sha512$50000$MyUpcsuC005nrCbJpbSJlg==$Hqcg6NlGSi+DslEqcszGTDRSlOuNLsPp5AO+LmM49NDc+OiPta4Ltgh2sVAQkHCBUIsqBodRaoS4b5wCWQw42w=='],
                [200000,'pbkdf2-sha512','$pbkdf2-sha512$200000$okqHCpO4yQwO8qd7b4RXnA==$QK4iwccZLOEe1+IqM6w8GqhyZaKy1T7vkT6FB6y/SNhE446UkhfsPLdywLYP8GxVhgCe+MV+o+ibEx3L0cpnAg==']
            ];
    }

    /**
     * Workaround as md5 does not have a salt
     */
    public function getSalt(string $hash) : string
    {
        $hashArr = explode('$', $hash);
        if (array_key_exists(3, $hashArr)) {
            return base64_decode($hashArr[3]);
        } else {
            return '';
        }
    }


    /**
     * @covers PasswordHash::hash
     */
    public function testHash()
    {

        //test default config
        $testHash = PasswordHash::hash('test');
        $this->assertEquals('098f6bcd4621d373cade4e832627b4f6', $testHash);

    }

    /**
     * @covers PasswordHash::verify
     */
    public function testVerifyCorrect()
    {
        // tests for short password
        $testArray =
        [
            [0,'md5','098f6bcd4621d373cade4e832627b4f6'],
            [50000,'md5-sha512','$md5-sha512$50000$TuXSCJnz0ZvJigjSOhKp3A==$1Zzi43hbly5+OYcdeeDQvZaARpjBZlWGfzRTqpW4Dhy1UDAejsJut0Dy+Wv/Ue3BE3dKz3XjhzM+0gSL8LK9RQ=='],
            [200000,'md5-sha512','$md5-sha512$200000$sBfM+NBfRsqSXwuCMP+gGQ==$Sr1bqZN4cXoSG8WIZLoOsmOUPEYXu5jYSfHi6Fzz0OnTBTOijh7h+CpJ8kofPMM59nf7eVku32m3Z/AivcXOuA=='],
            [50000,'pbkdf2-sha1','$pbkdf2-sha1$50000$LbD2N50f7pkUgue/SkDuqw==$6WkVQSh6359CrSOPEf+ItEEn+j8='],
            [200000,'pbkdf2-sha1','$pbkdf2-sha1$200000$liprMJBLrV9t3gl+T/9Ktg==$KoKX1VwaODZfab93SiOPZSs3Isg='],
            [50000,'pbkdf2-sha256','$pbkdf2-sha256$50000$y8oN21k4CgAwJXkA50SDNQ==$JqlNk+msf3kWV8x6UwYBNEgIjTOUfyJKOHC/B452+oU='],
            [50000,'pbkdf2-sha512','$pbkdf2-sha512$50000$MyUpcsuC005nrCbJpbSJlg==$Hqcg6NlGSi+DslEqcszGTDRSlOuNLsPp5AO+LmM49NDc+OiPta4Ltgh2sVAQkHCBUIsqBodRaoS4b5wCWQw42w=='],
            [200000,'pbkdf2-sha512','$pbkdf2-sha512$200000$okqHCpO4yQwO8qd7b4RXnA==$QK4iwccZLOEe1+IqM6w8GqhyZaKy1T7vkT6FB6y/SNhE446UkhfsPLdywLYP8GxVhgCe+MV+o+ibEx3L0cpnAg==']
        ];
        foreach ($testArray as $testHash){
            $GLOBALS['cfg']['iterations'] = $testHash[0];
            $GLOBALS['cfg']['password_hash_algorithm'] = $testHash[1];
            $this->assertEquals(true, PasswordHash::verify('test', $testHash[2]), 'Validation of hash failed: ' .$testHash[2]);
        }
    }

    /**
     * @covers PasswordHash::verify
     */
    public function testVerifyFalse()
    {
        // tests for short password
        $testArray =
        [
            [0,'md5','098f6bcd4621d373cade4e832627b4f7'],
            [50000,'md5-sha512','$pbkdf2-sha512$50000$SX9kiDHaUHqwILCFA1RI7Q==$bo6tl4q5J8VkGna/mdb+hDpRLy0wRY2MqNe8zIYWfynke8Ke8l+uu/w4hLQEpsKBUlcQ0rgXjOJWITwcMc1zIw=='],
            [200000,'md5-sha512','$md5-sha512$200000$okqHCpO4yQwO8qd7b4RXnA==$QK4iwccZLOEe1+IqM6w8GqhyZaKy1T7vkT6FB6y/SNhE446UkhfsPLdywLYP8GxVhgCe+MV+o+ibEx3L0cdnAg=='],
            [50000,'pbkdf2-sha1','$pbkdf2-sha1$50000$LbD2N50f7pkUgue/SkDuqw==$6WkVQSh6359CfSOPEf+ItEEn+j8='],
            [200000,'pbkdf2-sha1','$pbkdf2-sha1$200000$liprMJBLrV9t3gl+T/9Ktg==$KoKX1VwaODZgab93SiOPZSs3Isg='],
            [50000,'pbkdf2-sha256','$pbkdf2-sha256$50000$y8oN21k4CgAwJXkA50SDNQ==$JqlNk+msf5kWV8x6UwYBNEgIjTOUfyJKOHC/B452+oU='],
            [50000,'pbkdf2-sha512','$pbkdf2-sha512$50000$MyUpcsuC005nrCbJpbSJlg==$Hqcg6NlGSj+DslEqcszGTDRSlOuNLsPp5AO+LmM49NDc+OiPta4Ltgh2sVAQkHCBUIsqBodRaoS4b5wCWQw42w=='],
            [200000,'pbkdf2-sha512','$pbkdf2-sha512$200000$okqHCpO4yQwO8qd7b4RXnA==$QK4iwccYLOEe1+IqM6w8GqhyZaKy1T7vkT6FB6y/SNhE446UkhfsPLdywLYP8GxVhgCe+MV+o+ibEx3L0cpnAg==']
        ];
        foreach ($testArray as $testHash){
            $GLOBALS['cfg']['iterations'] = $testHash[0];
            $GLOBALS['cfg']['password_hash_algorithm'] = $testHash[1];
            $this->assertEquals(false, PasswordHash::verify('test', $testHash[2]), 'hash validated as OK but should not: ' . $testHash[2]);
        }
    }

    /**
     * @covers PasswordHash::getInfo
     */
    public function testGetInfo()
    {
        global $hashTestArray;
        //since function is private it has to be accessed via Reflection

        $passwordHashReflection = new ReflectionClass('LanSuite\PasswordHash');
        $getInfoMethod = $passwordHashReflection->getMethod('getInfo');

        foreach ($hashTestArray as $testHash) {
            $hashInfoArray = $getInfoMethod->invoke(null, $testHash[2]);
            $this->assertEquals($testHash[0], $hashInfoArray['iterations']);
            $this->assertEquals($testHash[1], $hashInfoArray['algo']);
            $hashArr = explode('$', $testHash[2]);
            $this->assertEquals($this->getSalt($testHash[2]), $hashInfoArray['salt']);
        }
    }

}