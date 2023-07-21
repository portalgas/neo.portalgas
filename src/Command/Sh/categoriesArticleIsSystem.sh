#
# chmod 774
#  /var/www/neo.portalgas/src/Command/Sh/categoriesArticleIsSystem.sh
#
source /var/portalgas/cron/config.conf

START=1
END=$TOT_ORGANIZATION

for (( ORGANIZATION_ID=$START; ORGANIZATION_ID<=$END; ORGANIZATION_ID++ ))
do
    # /var/www/neo.portalgas/bin/cake CategoriesArticleIsSystem $ORGANIZATION_ID
    /home/luca/progetti/neo.portalgas/bin/cake CategoriesArticleIsSystem $ORGANIZATION_ID
done
