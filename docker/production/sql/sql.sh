echo '*****************************'

mysql -u root -p$MYSQL_ROOT_PASSWORD -e \
"
CREATE USER '$DB_USERNAME'@'frontend' IDENTIFIED WITH mysql_native_password BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON $DB_DBNAME.* TO '$DB_USERNAME'@'frontend';
FLUSH PRIVILEGES;
" && \
echo "Successfully created frontend user"

mysql -u root -p$MYSQL_ROOT_PASSWORD -e \
"
CREATE USER '$DB_USERNAME'@'backend' IDENTIFIED WITH mysql_native_password BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON $DB_DBNAME.* TO '$DB_USERNAME'@'backend';
FLUSH PRIVILEGES;
" && \
echo "Successfully created backend user"


echo '*****************************'