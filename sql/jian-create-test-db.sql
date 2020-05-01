create database track_mate_test default character set utf8;
create user 'track_mate_user'@'localhost' identified by 'abc123';
grant all privileges on track_mate_test.* to 'track_mate_user'@'localhost' with grant option;