for ORGANIZATION_ID in %s
do
	php -f /var/www/portalgas/components/com_cake/app/Cron/index.php %s $ORGANIZATION_ID
done