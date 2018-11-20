def findPosition(playerPos):
    if playerPos == '1':
        return (255, 0, 0) #red
    elif playerPos == '2' or playerPos == '3':
        return (0, 255, 0) #green
    else:
        return (0, 255, 255) #azure