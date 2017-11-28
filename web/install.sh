#!/bin/bash
#---------------------------------------------------------------------------------------------------
# Install the tapas web site. Should use composer to install all of the php packages.
#---------------------------------------------------------------------------------------------------

# global setup
[ -z "$TAPAS_WEB_SERVER" ] && export TAPAS_WEB_SERVER=tapas.mit.edu
[ -z "$TAPAS_WEB_BASE" ]   && export TAPAS_WEB_BASE=/var/www/html
[ -z "$TAPAS_WEB_NAME" ]   && export TAPAS_WEB_NAME=tapas

# install all relevant tools
echo " RSYNC: rsync -Cavz ./ $TAPAS_WEB_SERVER:$TAPAS_WEB_BASE/$TAPAS_WEB_NAME >& /tmp/rsync.log"
              rsync -Cavz ./ $TAPAS_WEB_SERVER:$TAPAS_WEB_BASE/$TAPAS_WEB_NAME >& /tmp/rsync.log
mv /tmp/rsync.log /tmp/rsync-tapas.log

if [ "$1" != "lite" ]
then
  # install packages we need (fatfree)
  lib=$TAPAS_WEB_BASE/$TAPAS_WEB_NAME/app/lib
  execute="rm -rf $lib/fatfree; cd $lib; git clone https://github.com/bcosca/fatfree > message.bak"
  ssh $TAPAS_WEB_SERVER $execute
  execute="cd $lib/fatfree; git checkout tags/3.2.2 2>> ../message.bak"
  ssh $TAPAS_WEB_SERVER $execute
fi

# cleanup?

# restart server?

exit 0
