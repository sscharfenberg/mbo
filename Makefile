STAGING_HOST = mbo
STAGING_LOGS = /var/www/mbos/storage/logs
LOCAL_LOGS   = storage/logs

.PHONY: logs-staging

logs-staging:
	scp '$(STAGING_HOST):$(STAGING_LOGS)/*' $(LOCAL_LOGS)/