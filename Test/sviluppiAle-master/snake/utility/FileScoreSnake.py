import os

def findScores(conf):
    high_scores = []

    file_score = os.getcwd() + conf['path']
    with open(file_score) as f:
        lines = f.read().splitlines()
        for line in lines:
            pos,name,score = line.split(",")
            high_scores.append(int(score))

    return high_scores

def saveScore(conf, current_score, name):

    high_score = []

    file_score = os.getcwd() + conf['path']
    #transform lines file to list of array
    with open(file_score) as f:
        lines = f.read().splitlines()
        for line in lines:
            l = line.split(",")
            high_score.append(l)
        f.close()

    #sort list of array by score reverse
    high_score = sorted(high_score, key=lambda x: int(x[2]), reverse=True)
    #delete last position of highscore (min_score)
    high_score = high_score[:-1]
    #add the new score in highscore
    high_score.append([5,name,str(int(round(current_score)))]) #pos,name,score
    #sort list of array by score reverse
    new_high_score = sorted(high_score, key=lambda x: int(x[2]), reverse=True)
    #rewrite position
    for i in range(0, len(new_high_score)):
        new_high_score[i][0] = i + 1

    #write new score on file
    with open(file_score, "w") as f:
        for el in new_high_score:
            f.write(str(el[0]) + ',' + el[1] + ',' + el[2] + '\n')
        f.close()

def getHighScoresList(conf):

    high_scores_list = []

    file_score = os.getcwd() + conf['path']
    with open(file_score) as f:
        lines = f.read().splitlines()
        for line in lines:
            pos, name, score = line.split(",")
            app = {"pos": pos, "name": name, "score": score}
            high_scores_list.append(app)
        f.close()

    return high_scores_list
