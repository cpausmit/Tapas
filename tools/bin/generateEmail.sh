#!/bin/bash
#----------------------------------------------------------------------------------------------------
# Generate an email based on a few given templates. A few predefined standard tags will be replaced.
#----------------------------------------------------------------------------------------------------

SPOOL_DIR="./spool"
[ -z $TAPAS_TOOLS_DATA ] || SPOOL_DIR=$TAPAS_TOOLS_DATA/spool

DEBUG=0

TERM="$1"
NAME="$2"
ASSIGNMENT="$3"
FILENAME="$4"
TASKTYPE="$5"

ASSIGNMENT=`echo "$ASSIGNMENT" | tr '\n' '#'`

if [ "$DEBUG" == "1" ]
then
  echo "TERM: $TERM"
  echo "NAME: $NAME"
  echo "ASSIGNMENT:"
  echo "$ASSIGNMENT"
  echo ""
  echo "FILENAME:"
  echo "$FILENAME"
fi

# make sure we have a local spool directory
if ! [ -d $SPOOL_DIR ]
then
  mkdir "$SPOOL_DIR"
  if [ "$?" != "0" ]
  then
    echo " ERROR - could not create local spool directory ($SPOOL_DIR)"
    exit 1
  fi
fi

# find the relevant template
if [ "$TASKTYPE" == "part" ]
then
  template="$TAPAS_TOOLS_TEMPLATES/PartTa.eml"
else
  template="$TAPAS_TOOLS_TEMPLATES/FullTa.eml"
fi

termType=${TERM:0:1}
fileName=`echo $NAME | tr ' ' '_'`_${TERM}_.eml

if [ "$termType" == "I" ] # IAP at MIT is a special case
then
  sed -e "s/XX-TA-NAME-XX/$NAME/" -e "s/XX-COURSE-XX/$ASSIGNMENT/" \
         $TAPAS_TOOLS_TEMPLATES/PartTaIap.eml > "$SPOOL_DIR/$FILENAME.tmp"
else
  sed -e "s/XX-TA-NAME-XX/$NAME/" -e "s/XX-COURSE-XX/$ASSIGNMENT/" \
         $template > "$SPOOL_DIR/$FILENAME.tmp"
fi

cat "$SPOOL_DIR/$FILENAME.tmp" | tr '#' '\n' > "$SPOOL_DIR/$FILENAME"
rm  "$SPOOL_DIR/$FILENAME.tmp"

exit 0
