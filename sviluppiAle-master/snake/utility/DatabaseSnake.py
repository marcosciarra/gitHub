import MySQLdb, re

class DatabaseSnake:

    def __init__(self, conf):
        self.connection = MySQLdb.connect(conf['host'], conf['user'], conf['password'], conf['database'])
        self.cursor = self.connection.cursor()

    def findScores(self):

        high_scores = []

        query = "SELECT score FROM scores ORDER BY score DESC, record_date LIMIT 5"
        self.cursor.execute(query)
        for score_data in self.cursor.fetchall():
            high_scores.append(int(score_data[0]))

        for score_data in range(len(high_scores), 5):
            high_scores.append(0)

        return high_scores

    def saveScore(self,score, name, now):
        #elimina il punteggio + basso tra i 5 e poi aggiungi il nuovo
        try:
            query = "INSERT INTO scores (score, name, record_date) VALUES (%s, %s, %s)"
            self.cursor.execute(query, (score, name, str(now)))
            self.connection.commit()
        except:
            print 'Error Insert Score'
            self.connection.rollback()

    def cleanScore(self):
        try:
            query = "DELETE FROM scores WHERE id NOT IN(SELECT app.id FROM (SELECT id FROM scores ORDER BY score DESC LIMIT 5 )app)"
            self.cursor.execute(query)
            self.connection.commit()
        except ValueError:
            print 'Errore pulizia tabella'
            self.connection.rollback()

    def getHighScoresList(self):

        high_scores_list = []

        try:
            query = "SELECT name, score, DATE_FORMAT(record_date,'%d/%m/%Y') AS date FROM scores ORDER BY score DESC LIMIT 5"
            self.cursor.execute(query)
            pos = 1
            for score in self.cursor.fetchall():
                app = {"pos": pos,"name":score[0],"score":score[1],"date":score[2]}
                pos += 1
                high_scores_list.append(app)
            return high_scores_list

        except ValueError:
            print 'Errore recupero dati'
            self.connection.rollback()

    def query(self, query):
        cursor = self.connection.cursor(MySQLdb.cursors.DictCursor)
        cursor.execute(query)

        return cursor.fetchall()

    def __del__(self):
        self.connection.close()
