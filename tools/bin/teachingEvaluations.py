#!/usr/bin/env python
from bs4 import BeautifulSoup
import sys
import requests
import pandas as pd

# making a set of cookies (a jar) - take from live headers in the firefox addon
cookies = requests.cookies.RequestsCookieJar()
cookies.set('JSESSIONID',
            'a5d0b8cfc4a7b16c45b0619f83e1992e5db0acc1c6911213832f6fad3be60307.e38Qa3uQb3ePaO0MchiTe0',
            domain='mit.edu', path='/')
cookies.set('_shibsession_64656661756c7468747470733a2f2f656475636174696f6e7379732e6d69742e6564752f73686962626f6c657468',
            '_0a28bba83deb160fe51c291ca2b54e27',
            domain='mit.edu', path='/')

# the url
url = sys.argv[1]
r = requests.get(url,cookies=cookies)

#if r.text.match("Report for"):
#    print ' WORKED'
#else:
#    print " TEXT " + r.text

data = r.text
soup = BeautifulSoup(data,"lxml")

table = soup.find_all('table')[4]
rows = table.find_all('tr')[2:]
#
#print " Loop through rows"
for row in rows:

    name = row.find_all('td')[0].find_all('strong')[0].get_text()
    last_name = name.split(',')[0]
    first_name = name.split(',')[-1]

    overall_grade = float(row.find_all('td')[4].find_all('span')[0].get_text())

    print "%20s %20s %4.1f"%(last_name, first_name, overall_grade)
#
