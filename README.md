PhiTrac
=======

A light bugtracker built with Symfony2.

## Install
- Import Symfony2 using composer : ```composer install```
- Create database : ```doctrine:database:create```
- Create schema of database : ```doctrine:schema:create```
- Go to phpMyAdmin, and run this query in your database: 
  
  INSERT INTO User (username, name, firstname, password, salt, roles, slug) VALUES ('admin', 'admin', 'admin', '$2y$12$9b425f9a002f8f2f2defcOclstis6QzOohcMgP/S2Ti/dVtrjisxy', '9b425f9a002f8f2f2defcde1240e1e2773fac2aa5d00c32127c3e04f74d562b2', 'a:1:{i:0;s:10:"ROLE_ADMIN";}', 'admin')
- Go to [http://localhost/PhiTrac/web/app_dev.php/](http://localhost/PhiTrac/web/app_dev.php/).
- Login with username *admin* and password *admin*.
- Go to [Administration](http://localhost/PhiTrac/web/app_dev.php/admin), create your profile with the category *Administrator*.
- Logout and login with your credentials. You can now delete the useless admin profil, create other users, and use fully PhiTrac !
