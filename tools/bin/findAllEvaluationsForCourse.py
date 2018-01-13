#!/usr/bin/env python
from bs4 import BeautifulSoup
import sys, re, os
import requests
import pandas as pd

def getCookies(cookie_file):

    cookies = requests.cookies.RequestsCookieJar()

    with open(cookie_file,"r") as f:
        data = f.read()

    lines = data.split(";")
    for cookie in lines:
        cookie = cookie.strip()
        (cookie_key,cookie_value) = cookie.split('=')
        cookies.set(cookie_key,cookie_value,domain='mit.edu', path='/')
        print cookie_key + " --> " + cookie_value

    return cookies

def getEvaluationsForSubject(url,cookies):

    r = requests.get(url,cookies=cookies)
    data = r.text
    soup = BeautifulSoup(data,"lxml")
    
    table = soup.find_all('table')[4]
    rows = table.find_all('tr')[2:]

    for row in rows:
        tds = row.find_all('td')
        if len(tds)>0:                              # make sure we do not have crashes
            strongs = tds[0].find_all('strong')
            if len(strongs)>0:                      # make sure we do not have crashes
                name = strongs[0].get_text()
                last_name = name.split(',')[0]
                first_name = name.split(',')[-1]
                overall_grade = float(row.find_all('td')[4].find_all('span')[0].get_text())
                
                print "%20s %20s %4.1f"%(last_name, first_name, overall_grade)
        
#===================================================================================================
#                                       M A I N
#===================================================================================================
# get our cookies
cookies = getCookies("/home/paus/.cookies")

# command line
term = sys.argv[1]
print " TERM: " + term

# base
trunc = 'https://edu-apps.mit.edu/ose-rpt/'
evaluation_url = 'subjectEvaluationSearch.htm?'
parameters = 'departmentId=+++8&search=Search&termId=%s&subjectCode=&instructorName='%term

# the url
url = trunc + evaluation_url + parameters

# get the page output
r = requests.get(url,cookies=cookies)

data = r.text

# scrape it
soup = BeautifulSoup(data,"lxml")
paras = soup.find_all('p')
for row in paras:
    for link in row.find_all('a'):
        evaluation_link = link.get('href')
        if 'subjectId' in evaluation_link:

            p = re.compile('subjectId=.*$')
            matches = p.findall(evaluation_link)
            if len(matches) == 0:
                p = re.compile('subjectId=.*&')
                matches = p.findall(evaluation_link)
            if len(matches) > 0:
                print " %s -- %s"%(term,matches[0])
                getEvaluationsForSubject(trunc+evaluation_link,cookies)            
