echo '*****************************'

mysql -u root -p$MYSQL_ROOT_PASSWORD -e \
"
CREATE USER '$DB_USERNAME'@'%' IDENTIFIED WITH mysql_native_password BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON $DB_DBNAME.* TO '$DB_USERNAME'@'%';
FLUSH PRIVILEGES;
" && \
echo "Successfully created user"
echo '*****************************'