#!/usr/bin/env python
import sys
import Evaluation
import Database

# Open database connection
db = Database.DatabaseHandle()

# Simple
a = Database.Assignment('S9999','S9999-8.011-Lec-1','paus@mit.edu',-1)

# fails
a.insertDb(db)

# work
#a.selectDb(db,'S2017-8.011-Lec-2')
#a.show()

# fails
#a.updateDb(db)

# fails
#a.deleteDb(db)

# finish
db.disco()
sys.exit()
