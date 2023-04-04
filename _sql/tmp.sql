update k_organizations set paramsConfig = CONCAT(SUBSTRING(paramsConfig, 1, LENGTH(paramsConfig)-1), ',"ggArchiveStatics":"35"}') where type = 'GAS';
