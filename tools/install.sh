#!/bin/bash
#---------------------------------------------------------------------------------------------------
# Install the tapas interactive tools.
#---------------------------------------------------------------------------------------------------
# Configure and install
if [ -z "$1" ]
then
  echo " ERROR -- first parameter is the data area for the TAPAS system."
  echo "          ex. /home/paus/Documents/mit/department/ta"
  exit 1
fi
DATA="$1"

# generate the setup file
rm -f setup.sh
touch setup.sh
echo "# CAREFUL THIS FILE IS GENERATED AT INSTALL"                 >> setup.sh
echo "export TAPAS_TOOLS="`pwd`                                    >> setup.sh
echo "export TAPAS_TOOLS_BIN="`pwd`/bin                            >> setup.sh
echo "export TAPAS_TOOLS_PYTHON="`pwd`/python                      >> setup.sh
echo "export TAPAS_TOOLS_TEMPLATES="`pwd`/templates                >> setup.sh
echo "export TAPAS_TOOLS_DATA=$DATA"                               >> setup.sh
echo ""                                                            >> setup.sh
echo "export TAPAS_TOOLS_DEPEML=paus@mit.edu,sahughes@mit.edu,cmodica@mit.edu,phys-finance@mit.edu,phys-gradappts@mit.edu,ktanaka@mit.edu,nboyce@mit.edu" \
                                                                   >> setup.sh
echo ""                                                            >> setup.sh
echo "export PATH=\"\${PATH}:\${TAPAS_TOOLS_BIN}\""                >> setup.sh
echo "export PYTHONPATH=\"\${PYTHONPATH}:\${TAPAS_TOOLS_PYTHON}\"" >> setup.sh
echo ""                                                            >> setup.sh

exit 0
