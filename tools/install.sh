#!/bin/bash
#---------------------------------------------------------------------------------------------------
# Install the tapas interactive tools.
#---------------------------------------------------------------------------------------------------

# generate the setup file
rm -f setup.sh
touch setup.sh
echo "# CAREFUL THIS FILE IS GENERATED AT INSTALL"                 >> setup.sh
echo "export TAPAS_TOOLS="`pwd`                                    >> setup.sh
echo "export TAPAS_TOOLS_BIN="`pwd`/bin                            >> setup.sh
echo "export TAPAS_TOOLS_PYTHON="`pwd`/python                      >> setup.sh
echo "export TAPAS_TOOLS_TEMPLATES="`pwd`/templates                >> setup.sh
echo ""                                                            >> setup.sh
echo "export PATH=\"\${PATH}:\${TAPAS_TOOLS_BIN}\""                >> setup.sh
echo "export PYTHONPATH=\"\${PYTHONPATH}:\${TAPAS_TOOLS_PYTHON}\"" >> setup.sh
echo ""                                                            >> setup.sh

exit 0
