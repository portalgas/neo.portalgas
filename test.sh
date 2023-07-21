source /var/portalgas/cron/config.conf

START=1
END=$TOT_ORGANIZATION

for (( ORGANIZATION_ID=$START; ORGANIZATION_ID<=$END; ORGANIZATION_ID++ ))
do
    
	echo "organizzazione"
	echo $ORGANIZATION_ID
    # php -f /var/www/neo.portalgas/bin/cake CategoriesArticleIsSystem $ORGANIZATION_ID
    php -f /home/luca/progetti/neo.portalgas/bin/cake CategoriesArticleIsSystem $ORGANIZATION_ID
done
