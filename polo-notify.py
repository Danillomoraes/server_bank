# Start Configuration

POLONIEX_KEY = '2TX4V6RM-55E44XJJ-KLD0WO87-D4JBH9ZA'
POLONIEX_SECRET = '3a6b0b1865a0b91c152f8afb0dfaa80d13d1199d4731f25c409e2afa6b62aa23ff7dcec674ca89c3fa8e8c16d068364ca4ba9c7bca64bb61ff0ea98c6f9f8f31'

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

if NOTIFY_METHOD == 'email':
  import sendgrid
  from sendgrid.helpers.mail import *
elif NOTIFY_METHOD == 'pushed':
  import requests
else:
  print("NOTIFY_METHOD must be set to either 'email' or 'pushed'.")
  sys.exit(1)


sys.stdout.flush()

polo = Poloniex()
myCoach = Coach()

polo.Key = POLONIEX_KEY
polo.Secret = POLONIEX_SECRET

polo.public = Poloniex(coach=myCoach)
polo.private = Poloniex(polo.Key, polo.Secret, coach=myCoach)

currentUpdatePeriod = UPDATE_PERIOD_SECS

# Setup done, enter main loop
while True:
  if BALANCE_REPORTING:
    balance = polo.private.returnBalances()
    balanceETH = balance['ETH']
    balanceBTC = balance['BTC']
    balanceUSDT = balance['USDT']
    #balances = polo.private.returnCompleteBalances()

  dt_now = datetime.datetime.now(datetime.timezone.utc)
  ts_now = dt_now.timestamp() # works if Python >= 3.3
  ts_prev = ts_now - currentUpdatePeriod - 2 # 2s hysteresis to account for delays

  print(datetime.datetime.now())

  #returnTradeHistory occasionally throws "StopIteration" and "ValueError" while decoding
  try:
     tradehistory = polo.private.returnTradeHistory("all", start=ts_prev)
     print("tradehistory OK")
  except Exception:
    print("Failed to get trade history. Will try again next time.")
    currentUpdatePeriod += UPDATE_PERIOD_SECS


  currentUpdatePeriod = UPDATE_PERIOD_SECS

  if BALANCE_REPORTING:
    print("I have" , balanceETH ," ETH")
    print("I have" , balanceBTC ," BTC")
    print("I have" , balanceUSDT ," USDT")

  if len(tradehistory) >= 1:
    nicetradehistory = pprint.pformat(tradehistory)
    print (nicetradehistory)

    if NOTIFY_METHOD == 'email':
      print("Trades / Send email")
      sg = sendgrid.SendGridAPIClient(apikey='add_your_sendgrid_api_key_here')
      from_email = Email(EMAIL_FROM)
      subject = "Poloniex Status / Order Update"
      to_email = Email(EMAIL_TO)
      if BALANCE_REPORTING:
        content = "BTC: " + balanceBTC + "\n\nETH: " + balanceETH + "\n\nUSDT " + balanceUSDT + "\n\n" + nicetradehistory
      else:
        content = nicetradehistory
      email_content = Content("text/plain", content)
      #content = Content("text/plain", "BTC: ")
      mail = Mail(from_email, subject, to_email, email_content)
      response = sg.client.mail.send.post(request_body=mail.get())
      print(response.status_code)
      print(response.body)
      print(response.headers)

    elif NOTIFY_METHOD == 'pushed':
      print("Trades / Send pushed notification")

      # Compress history into just the essentials
      content = ""
      for market in tradehistory:
        content += market + ": "
        trades = dict()
        for trade in tradehistory[market]:
          if not trade['type'] in trades:
            trades[trade['type']] = dict()
          if not trade['rate'] in trades[trade['type']]:
            trades[trade['type']][trade['rate']] = 0.0
          trades[trade['type']][trade['rate']] += float(trade['total'])
        for type in trades:
          content += type + "("         
          for rate in trades[type]:
            content += str(trades[type][rate]) + "@" + rate.rstrip("0") + " "
          content = content.rstrip() # remove trailing space
          content += ") "
      content = content.rstrip() # remove tailing space

      payload = {
        "app_key": PUSHED_APP_KEY,
        "app_secret": PUSHED_APP_SECRET,
        "target_type": "app",
        "content": content
      }
      r = requests.post("https://api.pushed.co/1/push", data=payload)
      print("Pushed request OK")
      print(r.text)

  else:
    try:
      openO = polo.private.returnOpenOrders("all")
      print = ("ReturnOpenOrders OK")
      time.sleep(1)

      for market,rate in openO.items():
        if rate:
            pairB = market
            pairRate = rate[0].get("rate","none")


      returnTicker = polo.public.returnTicker()
      print = ("returnTicker OK")
      time.sleep(1)
      
      for pair, rate in returnTicker.items():
        if pairB == pair:
          #print(rate)
          rrate =rate.get("last", "none")
          print("1: "+rrate)
          print("2: "+pairRate)
          if float(rrate)/float(pairRate) >= 0.95:
            content = "Cotação atual proximo ao valor da order, Market: "+pair+", Valor da Order: "+pairRate+", Valor da Cotação atual: "+rrate
            payload = {
              "app_key": PUSHED_APP_KEY,
              "app_secret": PUSHED_APP_SECRET,
              "target_type": "app",
              "content": content
             }
            r = requests.post("https://api.pushed.co/1/push", data=payload)
            #print(r.text)
            pprint.pformat(content)

      returnT = pprint.pformat(returnTicker)
      #newcontent = pprint.pformat(openO)
      #print(returnT)
      #print(pairB)
      #print(pairRate)
      print("No Trades")
    finally:
      time.sleep(UPDATE_PERIOD_SECS)
