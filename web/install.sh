#!/bin/bash
#---------------------------------------------------------------------------------------------------
# Install the tapas web site. Should use composer to instal all of the php packages.
#---------------------------------------------------------------------------------------------------

# global setup
[ -z "$TAPAS_WEB_SERVER" ] && export TAPAS_WEB_SERVER=t3serv012.mit.edu
[ -z "$TAPAS_WEB_BASE" ]   && export TAPAS_WEB_BASE=/var/www/html
[ -z "$TAPAS_WEB_NAME" ]   && export TAPAS_WEB_NAME=tapas

# install all relevant tools
echo " RSYNC: rsync -Cavz ./ $TAPAS_WEB_SERVER:$TAPAS_WEB_BASE/$TAPAS_WEB_NAME >& /tmp/rsync.log"
              rsync -Cavz ./ $TAPAS_WEB_SERVER:$TAPAS_WEB_BASE/$TAPAS_WEB_NAME >& /tmp/rsync.log
mv /tmp/rsync.log /tmp/rsync-tapas.log

## install packages we need (fatfree)
ssh $TAPAS_WEB_SERVER "cd $TAPAS_WEB_BASE/$TAPAS_WEB_NAME/app/lib; git clone https://github.com/bcosca/fatfree; cd fatfree; git checkout tags/3.2.2"
#ssh $TAPAS_WEB_SERVER "chown apache:apache $TAPAS_WEB_BASE/$TAPAS_WEB_NAME/app/tmp"

# cleanup?

# restart server?

exit 0
