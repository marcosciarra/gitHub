import os, json

class ReadConf:

    def __init__(self):
        #print 'ReadConf'
        with open(os.getcwd()+"/utility/conf.json") as conf:
            c = json.load(conf)
            self.database = c['database']
            self.file = c['file']
