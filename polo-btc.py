# Start Configuration
# -*- coding: utf-8 -*-
import json


with open('keys.json') as json_data:
    d = json.load(json_data)

POLONIEX_KEY = d['poloniex_key']
POLONIEX_SECRET = d['poloniex_secret']

BALANCE_REPORTING = False
UPDATE_PERIOD_SECS = 60

NOTIFY_METHOD = 'pushed'

EMAIL_FROM = 'from@email.com'
EMAIL_TO = 'to@email.com'

PUSHED_APP_KEY = 'CVyAAz8Zu4UTtJ5xuuTW'
PUSHED_APP_SECRET = '5NARuDYYwJOodHu1da4Dg38SUQgaqpJvTDJpMRl0dZcHT0ntR8LK38AWfDrzKe6H'

# End Configuration

from poloniex import Poloniex, Coach
import datetime
import os
import time
import pprint
import sys
import json
import requests



sys.stdout.flush()

polo = Poloniex()
myCoach = Coach()

polo.Key = POLONIEX_KEY
polo.Secret = POLONIEX_SECRET

polo.public = Poloniex(coach=myCoach)
polo.private = Poloniex(polo.Key, polo.Secret, coach=myCoach)

currentUpdatePeriod = UPDATE_PERIOD_SECS

i = 0

while True:

	dt_now = datetime.datetime.now(datetime.timezone.utc)
	ts_now = dt_now.timestamp()
	ts_prev = ts_now - currentUpdatePeriod -2

	btc_now = polo.returnTicker()['USDT_BTC']
	btc_last = btc_now.get('last', 'none')
	btc_high = btc_now.get('high24hr', 'none')

	if float(btc_last)/float(btc_high) <= 0.90:
		content = "BTC em desvalorização, last: "+btc_last+", high24h: "+btc_high
		payload = {
        "app_key": PUSHED_APP_KEY,
        "app_secret": PUSHED_APP_SECRET,
        "target_type": "app",
        "content": content
		}
		r = requests.post("https://api.pushed.co/1/push", data=payload)
		print(content)
	print(datetime.datetime.now())
	print("BTC_USTD now: "+str(btc_now.get('last', 'none')))
	print("BTC_high: "+str(btc_high))

	time.sleep(60)

	#i= i+1
