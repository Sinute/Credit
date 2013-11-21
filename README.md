Credit
======

a system to gain credit from forum like discuz or phpwind (with an example to forum 'Saraba1st'

# Install
1. download [SPPHP] and edit 'SP_ROOT' in index.php to SPPHP path
2. enable rewrite mod
3. import sql file in protected/data/schema.mysql.sql
4. config protected/plugin/PDB/PDB.config.php
5. config protected/plugin/PMemcache/PMemcache.config.php
6. be sure protected/content is writable
7. add index.php to crontab with param Cron like '*/10 * * * * php index.php Cron'

[SPPHP]: https://github.com/Sinute/SPPHP
