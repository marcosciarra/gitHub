#imports
import os, pygame, sys, MySQLdb, datetime, random
from utility import inputbox, ReadConf, DatabaseSnake, FileScoreSnake, functions
from pygame.locals import *


pygame.init()

#-------------------------------------------------LOAD IMAGES-----------------------------------------------------------
def load_image(name, colorkey=None):
    "loads an image, prepares it for play"
    fullname = os.path.join('data/img', name)
    try:
        surface = pygame.image.load(fullname)
    except pygame.error:
        raise SystemExit, 'Could not load image "%s" %s'%(file, pygame.get_error())
    if colorkey is not None:
        if colorkey is -1:
            colorkey = surface.get_at((0,0))
        surface.set_colorkey(colorkey, RLEACCEL)
    return surface.convert()

def load_images(*files):
    imgs = []
    for file in files:
        imgs.append(load_image(file, -1))
    return imgs

def repaint_screen():
    all.clear(screen, background)
    dirty = all.draw(screen)
    pygame.display.update(dirty)

#---------------------------------------------------LOAD SOUND----------------------------------------------------------
def load_sound(name):
    class NoneSound:
        def play(self): pass
    if not pygame.mixer or not pygame.mixer.get_init():
        return NoneSound()
    fullname = os.path.join('data/sound', name)
    try:
        sound = pygame.mixer.Sound(fullname)
    except pygame.error, message:
        print 'Cannot load sound:', fullname
        raise SystemExit, message
    return sound

#------------------------------------INIT OBJECT (snake,food, bonus, score, text)---------------------------------------
class Centipede(pygame.sprite.Sprite):
    images = []

    def __init__(self):
        pygame.sprite.Sprite.__init__(self) #call Sprite initializer
        self.image = self.images[0]
        self.rect = self.image.get_rect()
        self.rect[0] = 290
        self.rect[1] = 290
        self.move = [0,-step]
        self.update_call = 0
        self.img_index = 0

    def update(self):
        self.update_call += 1
        pygame.event.pump()
        pos = [0,0,0,0]
        if self.rect[0]%20 == 10 and self.rect[1]%20 == 10:
            pos = [pygame.key.get_pressed()[K_DOWN],pygame.key.get_pressed()[K_UP],\
                 pygame.key.get_pressed()[K_LEFT],pygame.key.get_pressed()[K_RIGHT]]
        if sum(pos)==1:
            self.move[1] = (pos[0]-pos[1])*step
            self.move[0] = (pos[3]-pos[2])*step
#        print self.rect
        # mouth open or shut
        if self.update_call == 5: 
            self.update_call =0
            if self.img_index:
                self.img_index = 0
            else:
                self.img_index = 1

        # makes the head face in the direction of movement 
        if self.move[0] == -step:
            self.image = self.images[self.img_index]
        elif self.move[0] == step:
            self.image = pygame.transform.flip(self.images[self.img_index],1,0)
        elif self.move[1] == step:
            self.image = pygame.transform.rotate(self.images[self.img_index],90)
        else:
            self.image = pygame.transform.rotate(self.images[self.img_index],-90)
            
        newpos = self.rect.move((self.move))
        self.rect = newpos

    def outside(self,x,y):
        if self.rect[0] < 30 or self.rect[0] > x or self.rect[1] < 30 or self.rect[1] > y:
            self.end('l')
            return True
        else:
            return False

    def end (self, param):
        if param == 'l':
            self.image = self.images[2] #collision with laser (wall)
        elif param == 's':
            self.image = self.images[3] #collision with self
        elif param == 'm':
            self.image = self.images[4] #collision with meteor

        self.rect = self.rect.move(-20,-20)
        
    def position(self):
        return self.rect[0], self.rect[1]

class Body(pygame.sprite.Sprite):
    images = []
 
    def __init__(self, start):
        pygame.sprite.Sprite.__init__(self, self.containers) #call Sprite initializer
        self.image = self.images[0]
        self.rect = self.image.get_rect()
        self.moves = []
        self.rect[0] = 290
        self.rect[1] = start
        for i in range (0,3):
            self.moves.append((290,start+(i*-step)))
     
    def move(self,xy):
        self.rect[0] = xy[0]
        self.rect[1] = xy[1]
        self.moves.append(xy)
        del self.moves[0]
        
class Food(pygame.sprite.Sprite):
    images = []
    def __init__(self):
        pygame.sprite.Sprite.__init__(self) #call Sprite initializer
        self.image = self.images[0]
        self.rect = self.image.get_rect()
        self.rect[0] = (random.randrange(1,28,1)*20)+10 #sx
        self.rect[1] = (random.randrange(1,28,1)*20)+16 #top
        
class Bonus(pygame.sprite.Sprite):
    images = []
    def __init__(self):
        pygame.sprite.Sprite.__init__(self) #call Sprite initializer
        self.image = self.images[0]
        self.rect = self.image.get_rect()
        self.rect[0] = (random.randrange(1,28,1)*20)+10 #sx
        self.rect[1] = (random.randrange(1,28,1)*20)+16 #top

class Meteor(pygame.sprite.Sprite):
    images = []
    def __init__(self):
        pygame.sprite.Sprite.__init__(self) #call Sprite initializer
        self.image = self.images[0]
        self.rect = self.image.get_rect()
        self.rect[0] = 550 - random.choice([100,150,200,250,300,350,400,450]) #sx
        self.rect[1] = 550 - random.choice([100,150,200,250,300,350,400,450]) #top

class Astronaut(pygame.sprite.Sprite):
    images = []
    def __init__(self):
        pygame.sprite.Sprite.__init__(self) #call Sprite initializer
        self.image = self.images[0]
        self.rect = self.image.get_rect()
        self.rect[0] = (random.randrange(1,28,1)*20)+10 #sx
        self.rect[1] = (random.randrange(1,28,1)*20)+16 #top

class Easteregg(pygame.sprite.Sprite):
    images = []
    def __init__(self):
        pygame.sprite.Sprite.__init__(self) #call Sprite initializer
        self.image = self.images[0]
        self.rect = self.image.get_rect()
        self.rect[0] =  (background.get_width() / 2) - 25
        self.rect[1] =  (background.get_height() / 2) - 120
        
class Score(pygame.sprite.Sprite):
    def __init__(self):
        pygame.sprite.Sprite.__init__(self)
        self.font = pygame.font.Font("data/font/8bitfont.ttf", 25)
        #self.font.set_italic(1)
        self.color = Color('white')
        self.lastscore = -1
        self.update()
        self.rect = self.image.get_rect().move(430, 5)

    def update(self):
        if score != self.lastscore:
            self.lastscore = score
            msg = "Score: %s" % str(int(score)).zfill(6)
            self.image = self.font.render(msg, 0, self.color)

class Text(pygame.sprite.Sprite):
    def __init__(self,status,bonus = 0):
        pygame.sprite.Sprite.__init__(self)

        size = 50
        color = (255, 242, 5)
        x = background.get_width() / 2
        y = background.get_height() / 2
        text = ''

        if status == 0:
            text = "Ti sei mangiato da solo!"
        elif status == 1:
            text = "Il laser ti ha fritto!"
        elif status == 2:
            text = "Pausa"
        elif status == 3:
            text = "Bonus %d" % bonus
        elif status == 4:
            text = "Hai colpito un meteorite"
        elif status == 5:
            text = "Bonus atronauta"
        elif status == 6:
            text = random.choice(['A', 'L', 'E', 'P', 'E', 'R', 'I'])
            size = 25
            if text == 'A' or text == 'P':
                size = 30
                color = (211, 11, 11)
            x = (random.randrange(1, 28, 1) * 20) + 16
            y = (random.randrange(1, 28, 1) * 20) + 16
        elif status == 7:
            text = "Hai trovato qualcosa.."
        elif status == 8:
            text = "EASTEREGG, HAI VINTO!"
        elif status == 9:
            text = "GAME OVER"
            color = (211, 11, 11)
            y = background.get_height() / 2 - 50

        self.font = pygame.font.Font("data/font/8bitfont.ttf", size)
        self.image = self.font.render(text, 1, color)
        self.rect = self.image.get_rect(centerx = x, centery = y)

class Display_text(pygame.sprite.Sprite):
    def __init__(self,text,position_top,position_left,size,colour):
        pygame.sprite.Sprite.__init__(self)
        self.font = pygame.font.Font("data/font/8bitfont.ttf", size)
        self.image = self.font.render(text,1,colour)
        self.rect = self.image.get_rect(left = position_left,top = position_top)

    def update(self,text,underscore,colour):
        text = text + underscore
        self.image = self.font.render(text, 1, colour)

class Display_help(pygame.sprite.Sprite):
    def __init__(self,text,position_top,position_left,size,colour):
        pygame.sprite.Sprite.__init__(self)
        self.font = pygame.font.Font("data/font/8bitfont.ttf", size)
        self.image = self.font.render(text,1,colour)
        self.rect = self.image.get_rect(left = position_left,top = position_top)

class Main_Image(pygame.sprite.Sprite):
    images=[]
    def __init__(self):
        pygame.sprite.Sprite.__init__(self) #call Sprite initializer
        self.image = self.images[0]
        self.rect = (30,30,540,540)

#=====================================================START GAME========================================================
def main(start):
    
    #initialize variables
    global screen, background, step, score, all, bodies
    begin = 1
    step = 10
    score = 0
    snake_alive = 1
    headmoves = [(290,300),(290,290)]
    clock = pygame.time.Clock()
    bodies = []
    all = pygame.sprite.OrderedUpdates()
    #bonus
    bonus_status = 0
    bonus_prob = 1000
    bonus_time = 0
    bonus = pygame.sprite.Sprite()
    #meteor
    meteor_status = 0
    meteor_prob = 1000
    meteor_time = 0
    meteor = pygame.sprite.Sprite()
    #astronaut
    astronaut_status = 0
    astronaut_prob = 5000
    astronaut_time = 0
    astronaut = pygame.sprite.Sprite()
    #easteregg
    astronaut_eaten = 0
    easteregg_prob = 1000
    easteregg_time = 0
    easteregg = pygame.sprite.Sprite()
    #text
    text_time = 0
    crash_text = pygame.sprite.Sprite()
    bonus_text = pygame.sprite.Sprite()
    meteor_text = pygame.sprite.Sprite()
    astronaut_text = pygame.sprite.Sprite()
    easteregg_text = pygame.sprite.Sprite()
    easteregg_win_text = pygame.sprite.Sprite()
    gameover_text = pygame.sprite.Sprite()

    #get main frame
    os.environ['SDL_VIDEO_CENTERED'] = 'anything'
    fullname = os.path.join('data/img', 'snake.png')
    pygame.display.set_icon(pygame.image.load(fullname))
    screen = pygame.display.set_mode((600,600))
    pygame.display.set_caption('SPACE SNAKE - developed by Peril')
    background = load_image('background.png')
    screen.blit(background,(0,0))
    pygame.display.flip()

    #load sounds
    insert_coin_sound = load_sound('coin.wav')
    eat_sound = load_sound('chomp.wav')
    self_crash_sound = load_sound('death.wav')
    laser_crash_sound = load_sound('laserDeath.wav')
    meteor_crash_sound = load_sound('meteorDeath.wav')
    bonus_sound = load_sound('bonus.wav')
    super_bonus_sound = load_sound('superbonus.wav')
    easter_egg_sound = load_sound('foundSmt.wav')
    easter_egg_win_sound = load_sound('easteregg.wav')
    #load images
    Centipede.images = load_images('head.gif','head2.gif','deathLaser.gif','deathSelf.gif','deathMeteor.gif')
    Food.images = [load_image('alien.gif',-1)]
    Body.images = [load_image('body.gif',-1)]
    Bonus.images = [load_image('ufo.gif',-1)]
    Meteor.images = [load_image('meteor.png',-1)]
    Astronaut.images = [load_image('astronaut.gif',-1)]
    Easteregg.images = [load_image('egg.png',-1)]
    Main_Image.images = [load_image('home.png')]
    
    pygame.mouse.set_visible(0)
    
    width = screen.get_width() - 50
    height = screen.get_height() - 50

    #start screen
    if start == 0:
        start = 1
        all.add(Main_Image())
        all.add(Display_text('Per iniziare a giocare premere INVIO',550,120,25,(Color(255, 138, 35))))
        repaint_screen()
        while 1:
            event = pygame.event.wait()
            if event.type == QUIT:
                sys.exit()
            if event.type == KEYDOWN and event.key == K_RETURN:
                insert_coin_sound.play()
                break   
        all.empty()
    
    #create head...
    centipede = Centipede()
    centirect = centipede.rect
    Body.containers = all
    
    #...and body...
    for i in range (3):
        bodies.append(Body(330+(i*20)))
    #...and some food
    food = Food()
    
    # make sure food isn't in same place as snake
    while 1:
        if centirect.colliderect(food.rect) or pygame.sprite.spritecollide(food,bodies,0) != []: 
            food.kill()
            food = Food()
        else:
            break
        
    all.add(food, centipede)
    
    # initialize score
    if pygame.font:
        score_instance = Score()
        all.add(score_instance)
        
    pygame.time.delay(400)
    pygame.event.clear()
    
    # main game loop
    while snake_alive:
        bonus_time -= 1
        meteor_time -= 1
        astronaut_time -= 1
        easteregg_time -= 1
        text_time -= 1
        clock.tick(25)

        centirect = centipede.rect
        
        #handles pause, exit
        for event in pygame.event.get():
            if event.type == QUIT:
                sys.exit()
            elif event.type == KEYDOWN and event.key == K_ESCAPE:
                sys.exit()
            elif event.type == KEYDOWN and event.key == K_SPACE:
                pause = 1
                if pygame.font:
                    pause_text=Text(2)
                    all.add(pause_text)
                all.clear(screen, background) 
                dirty = all.draw(screen)
                pygame.display.update(dirty)
                pygame.event.clear()
                while pause:
                    event = pygame.event.wait()
                    if event.type == QUIT:
                        sys.exit()
                    if event.type == KEYDOWN and event.key == K_SPACE:
                        pause = 0
                        pause_text.kill()
            #easter egg
            #elif event.type == KEYUP and event.key == K_a and astronaut_eaten > 2: ToDo scommentare
            elif event.type == KEYUP and event.key == K_a: #ToDo commentare
                egg = 1
                if pygame.font:
                    easter_egg_text=Text(7)
                    all.add(easter_egg_text)
                all.clear(screen, background)
                dirty = all.draw(screen)
                pygame.display.update(dirty)
                pygame.event.clear()
                easter_egg_sound.play()
                while egg:
                    event = pygame.event.wait()
                    if event.key == K_p:
                        egg = 0
                        easter_egg_text.kill()
                        all.remove(crash_text, food, bonus, meteor, meteor_text, astronaut, astronaut_text, easter_egg_text)
                        easter_egg_win_sound.play()
                        score = 999999
                        snake_alive = 0
                        easteregg_win_text = Text(8)
                        easteregg = Easteregg()
                        all.add(easteregg, easteregg_win_text)
                    else:
                        easter_egg_text.kill()
                        egg = 0

        all.update()
        
        # make body move
        lastmove = headmoves[0]        
        for body in bodies:
            body.move(lastmove)
            lastmove = body.moves[0]
        
        # update moves of head
        headmoves.append(centipede.position())
        del headmoves[0]
        
        # detects collision with the wall (laser)
        if centipede.outside(width,height):
            snake_alive = 0
            laser_crash_sound.play()
            #re-create head to make sure it's last sprite in all
            #and nothing is drawn over it
            all.remove(centipede)
            all.remove(score_instance)
            all.add(centipede)
            all.add(score_instance)
            centipede.end('l')
            if bonus_text.alive():
                bonus_text.kill()
            if astronaut_text.alive():
                astronaut_text.kill()
            if pygame.font:
                crash_text = Text(1)
                all.add(crash_text)
                gameover_text = Text(9)
                all.add(gameover_text)

        # collision between head and body
        if snake_alive != 0:
            smallcenti = centipede.rect.inflate(-15,-15)  
            for body in bodies:
                smallbody = body.rect.inflate(-15,-15)
                if smallbody.colliderect(smallcenti):
                    snake_alive = 0
                    self_crash_sound.play()
                    all.remove(centipede)
                    all.remove(score_instance)
                    all.add(centipede)
                    all.add(score_instance)
                    centipede.end('s')
                    if bonus_text.alive():
                        bonus_text.kill()
                    if astronaut_text.alive():
                        astronaut_text.kill()
                    if pygame.font:
                        crash_text = Text(0)
                        all.add(crash_text)
                        gameover_text = Text(9)
                        all.add(gameover_text)

        # repaint before you make snake grow
        # otherwise new body will show in default position
        # before being appended to snake                 
        repaint_screen()
                
        # check if food has been eaten
        # creates new food
        # makes body grow
        if snake_alive != 0 and centirect.colliderect(food.rect):
            food.kill()
            score = score + 1
            bonus_prob = bonus_prob - 1
            meteor_prob = meteor_prob - 1
            astronaut_prob = astronaut_prob - 1
            easteregg_prob = easteregg_prob - 1
            eat_sound.play()
            food = Food()
            bodies.append(Body(304))
            while 1:
                if bonus.alive():
                    if centirect.colliderect(food.rect) or pygame.sprite.spritecollide(food,bodies,0) != [] or bonusrect.colliderect(food.rect):
                        food.kill()
                        food = Food()
                    else:
                        break
                elif meteor.alive():
                    if centirect.colliderect(food.rect) or pygame.sprite.spritecollide(food,bodies,0) != [] or meteorrect.colliderect(food.rect):
                        food.kill()
                        food = Food()
                    else:
                        break
                elif astronaut.alive():
                    if centirect.colliderect(food.rect) or pygame.sprite.spritecollide(food,bodies,0) != [] or astronautrect.colliderect(food.rect):
                        food.kill()
                        food = Food()
                    else:
                        break
                else:
                    if centirect.colliderect(food.rect) or pygame.sprite.spritecollide(food,bodies,0) != []:
                        food.kill()
                        food = Food()
                    else:
                        break
            all.add(food)

        # --------------------------------display new bonus(only one at a time)-----------------------------------------
        if bonus_status == 0 and random.randrange(1,1000,1) > bonus_prob:
            bonus_status = 1
            bonus = Bonus()
            bonusrect = bonus.rect
            bonus_time = random.randrange(50,150,1)
            while 1:
                if meteor.alive():
                    if bonusrect.colliderect(food.rect) or bonusrect.colliderect(centipede.rect) or meteorrect.colliderect(bonus.rect):
                        bonus.kill()
                        bonus = Bonus()
                        bonusrect = bonus.rect
                    else:
                        break
                elif astronaut.alive():
                    if bonusrect.colliderect(food.rect) or bonusrect.colliderect(centipede.rect) or astronautrect.colliderect(bonus.rect):
                        bonus.kill()
                        bonus = Bonus()
                        bonusrect = bonus.rect
                    else:
                        break
                else:
                    if bonusrect.colliderect(food.rect) or bonusrect.colliderect(centipede.rect) or pygame.sprite.spritecollide(bonus,bodies, 0) != []:
                        bonus.kill()
                        bonus = Bonus()
                        bonusrect = bonus.rect
                    else:
                        break
            all.add(bonus)

        # kill bonus when time is up
        if bonus_time == 0:
            bonus.kill()
            bonus_status = 0

        # kill bonus text
        if text_time == 0:
            bonus_text.kill()

        # check if bonus has been eaten
        if bonus.alive() and snake_alive != 0:
            if bonusrect.colliderect(centipede.rect):
                bonus.kill()
                bonus_status = 0
                bonus_prob = 1000
                if bonus_text.alive():
                    bonus_text.kill()
                if astronaut_text.alive():
                    astronaut_text.kill()
                bonus_points = round(bonus_time/5+2)
                score = score + bonus_points
                bonus_sound.play()
                bonus_text = Text(3,bonus_points)
                text_time = 25
                all.add(bonus_text)
                bodies.append(Body(304))
                bonus_time = 0

        # --------------------------------------display new meteor(only one at a time)----------------------------------
        if meteor_status == 0 and random.randrange(1,1000,1) > meteor_prob:
            meteor_status = 1
            meteor = Meteor()
            meteorrect = meteor.rect
            meteor_time = 500
            while 1:
                if bonus.alive():
                    if meteorrect.colliderect(food.rect) or meteorrect.colliderect(centipede.rect) or meteorrect.colliderect(bonus.rect):
                        meteor.kill()
                        meteor = Meteor()
                        meteorrect = meteor.rect
                    else:
                        break
                elif astronaut.alive():
                    if meteorrect.colliderect(food.rect) or meteorrect.colliderect(centipede.rect) or meteorrect.colliderect(astronaut.rect):
                        meteor.kill()
                        meteor = Meteor()
                        meteorrect = meteor.rect
                    else:
                        break
                else:
                    if meteorrect.colliderect(food.rect) or meteorrect.colliderect(centipede.rect) or pygame.sprite.spritecollide(meteor,bodies, 0) != []:
                        meteor.kill()
                        meteor = Meteor()
                        meteorrect = meteor.rect
                    else:
                        break
            all.add(meteor)

        # kill meteor when time is up
        if meteor_time == 0:
            meteor.kill()
            meteor_status = 0

        # kill meteor text
        if text_time == 0:
            meteor_text.kill()

        # check if meteor has been hit
        if meteor.alive() and snake_alive != 0:
            if meteorrect.colliderect(centipede.rect):
                snake_alive = 0
                meteor_crash_sound.play()
                all.remove(centipede)
                all.remove(score_instance)
                all.add(centipede)
                all.add(score_instance)
                centipede.end('m')
                if bonus_text.alive():
                    bonus_text.kill()
                if astronaut_text.alive():
                    astronaut_text.kill()
                if pygame.font:
                    meteor_text = Text(4)
                    all.add(meteor_text)
                    gameover_text = Text(9)
                    all.add(gameover_text)

                repaint_screen()

        # --------------------------------display super bonus(Astronaut)-----------------------------------------
        if astronaut_status == 0 and random.randrange(1,5000,1) > astronaut_prob:
            astronaut_status = 1
            astronaut = Astronaut()
            astronautrect = astronaut.rect
            astronaut_time = random.randrange(40,150,1)
            while 1:
                if bonus.alive():
                    if astronautrect.colliderect(food.rect) or astronautrect.colliderect(centipede.rect) or astronautrect.colliderect(bonus.rect):
                        astronaut.kill()
                        astronaut = Astronaut()
                        astronautrect = astronaut.rect
                    else:
                        break
                elif meteor.alive():
                    if astronautrect.colliderect(food.rect) or astronautrect.colliderect(centipede.rect)  or astronautrect.colliderect(meteor.rect):
                        astronaut.kill()
                        astronaut = Astronaut()
                        astronautrect = astronaut.rect
                    else:
                        break
                else:
                    if astronautrect.colliderect(food.rect) or astronautrect.colliderect(centipede.rect) or pygame.sprite.spritecollide(astronaut, bodies, 0) != []:
                        astronaut.kill()
                        astronaut = Astronaut()
                        astronautrect = astronaut.rect
                    else:
                        break
            all.add(astronaut)

        # kill astronaut when time is up
        if astronaut_time == 0:
            astronaut.kill()
            astronaut_status = 0

        # kill astronaut text
        if text_time == 0:
            astronaut_text.kill()

        # check if astronaut has been eaten
        if astronaut.alive() and snake_alive != 0:
            if astronautrect.colliderect(centipede.rect):
                astronaut.kill()
                astronaut_status = 0
                astronaut_prob = 5000
                score += 100
                if bonus_text.alive():
                    bonus_text.kill()
                if astronaut_text.alive():
                    astronaut_text.kill()
                super_bonus_sound.play()
                astronaut_text = Text(5)
                text_time = 25
                all.remove(Text, bodies)
                all.add(astronaut_text)
                bodies = []
                #bodies.append(Body(304)) scegliere quanti body attaccare al superbonus
                astronaut_time = 0
                astronaut_eaten = astronaut_eaten +1

        # ---------------------------------------display help text for ee-----------------------------------------------

        if random.randrange(1,1000,1) > easteregg_prob:
            easteregg_text.kill()
            easteregg_text = Text(6)
            all.add(easteregg_text)
            text_time = 25
            easteregg_time = random.randrange(40,150,1)

        # kill letter
        if text_time == 0:
            easteregg_text.kill()
            easteregg_prob = 1000

        # -----------------------------------------------GAME OVER------------------------------------------------------
        if snake_alive == 0:

            while 1:
                event = pygame.event.wait()
                if event.type == QUIT:
                    sys.exit()
                if event.type == KEYDOWN:
                    break

            #remove game object
            all.remove(crash_text, centipede, bodies, food, bonus, meteor, meteor_text, astronaut, astronaut_text, easteregg_win_text, easteregg, gameover_text)

            #SAVE
            save_scores(score) #check score > lower score in highscore and save

            #show highscore
            repaint_screen()
            background = load_image('highscore.jpg')
            screen.blit(background, (0, 0))
            pygame.display.flip()

            top = 230
            for el in get_ranking():
                playerPos = str(el.get('pos'))
                playerName = str(el.get('name'))
                playerScore = str(el.get('score')).zfill(6)
                left = 90
                colorText = functions.findPosition(playerPos)
                all.add(Display_text(playerPos, top, left, 50, colorText))
                all.add(Display_text(playerName.upper(), top, left + 50, 50, colorText))
                all.add(Display_text(playerScore, top, left + 260, 50, colorText))
                top += 30

            all.add(Display_text('Vuoi giocare ancora?  (y/n)',470, 130, 30,Color(255, 138, 35)))
            repaint_screen()

        if begin == 1:
            begin = 0
            pygame.time.delay(1000)

    # ------------------------------------------------------MANAGE SPEED-----------------------------------------------------

        # def increaseSpeed(score):
        #     if score < 10:
        #         clock.tick(25)
        #     elif score > 9 and score < 25:
        #         clock.tick(2)
        #     elif score > 24 and score < 40:
        #         clock.tick(3)
        #     elif score > 60:
        #         clock.tick(4)


#------------------------------------------------MANAGE SCORE [DB/FILE]-------------------------------------------------

#try db connection
def check_db_connection(conf):
    try:
        dbSnake = MySQLdb.connect(conf['host'], conf['user'], conf['password'], conf['database'], connect_timeout=10)
    except:
        return 0
    else:
        dbSnake.close()
        return 1

#save score
def save_scores(current_score):

    c = ReadConf.ReadConf() #get configuration fronm file
    db_score = check_db_connection(c.database) #check connection [1/0]

    #get highscores
    if db_score:
        db = DatabaseSnake.DatabaseSnake(c.database)
        high_scores = db.findScores() #from db
    else:
        high_scores = FileScoreSnake.findScores(c.file) #from file

    min_score = min(high_scores) #get min_score from highscores

    #check current_score > min_score
    if current_score > min_score:
        nome = inputbox.ask(screen) #get player name
        #save score & name
        if db_score:
            db = DatabaseSnake.DatabaseSnake(c.database)
            db.saveScore(current_score, nome[:6], datetime.datetime.now()) #to db
            db.cleanScore() #clear db over 5 row
        else:
            FileScoreSnake.saveScore(c.file, current_score, nome[:6]) #to file

#get highscore to show the ranking
def get_ranking():

    c = ReadConf.ReadConf() #get configuration fronm file
    db_score = check_db_connection(c.database) #check connection [1/0]

    if db_score:
        db = DatabaseSnake.DatabaseSnake(c.database)
        return db.getHighScoresList() #from db
    else:
        return FileScoreSnake.getHighScoresList(c.file) #from file


#--------------------------------------------------MAIN [START GAME]----------------------------------------------------

# start game when loaded first time              
if __name__ == '__main__': main(0)

# handles 'play again' situation
end = 1
while end:
    event = pygame.event.wait()
    if event.type == QUIT:
        end = 0
    if event.type != KEYDOWN:
        continue
    if event.key == K_ESCAPE:
        end = 0
    elif event.key == K_n:
        end = 0
    elif event.key == K_y:
        main(1)
            


