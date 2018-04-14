UPGRADE FROM 4.x to 5.0
=======================

Database
------

Many LanSuite installations have database table prefixes.
We assume that your database table prefix is `ls_`.
If this is different for your installation, please change the table names in the queries accordingly.

 * The column `cfg_key` in table `config_selections` was extended from `CHAR(16)` to `VARCHAR(50)`

   To upgrade, execute the query

   ```sql
   ALTER TABLE `ls_config_selections` MODIFY `cfg_key` VARCHAR(50);
   ```
 * The column `cfg_type` in table `config` was extended from `VARCHAR(16)` to `VARCHAR(50)`

   To upgrade, execute the query

   ```sql
   ALTER TABLE `ls_config` MODIFY `cfg_type` VARCHAR(50);
   ```
