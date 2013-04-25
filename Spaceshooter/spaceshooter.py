import pygame
import os
import math
from pygame import *
import Image 


DISPLAY = (800, 640)
SCALE = 32

def load_image(name, colorkey=None):
    print "loading %s" %name
    fullname = os.path.join('images', name)
    try:
        image = pygame.image.load(fullname)
    except pygame.error, message:
        print 'Cannot load image:', name
        raise SystemExit, message
    image = image.convert()
    if colorkey is not None:
        if colorkey is -1:
            colorkey = image.get_at((0,0))
        image.set_colorkey(colorkey, RLEACCEL)
    return image, image.get_rect()

"""
THIS AREA NEEDS WORK!
def load_sliced_sprites(self, w, h, filename):
    '''
    Specs:
        Master can be any height.
        Sprites frames width must be the same width
        Master width must be len(frames)*frame.width
    Assuming your resources directory is names "resources"
    '''

    images = []
    master_image = pygame.image.load(os.path.join('images', filename)).convert_alpha()

    master_width, master_height = master_image.get_size()
    for i in xrange(int(master_width/w)):
        images.append(master_image.subsurface((i*w, 0, w, h))
    return images
"""

def main():
    pygame.init()

    # creat screen
    screen = display.set_mode(DISPLAY)
    display.set_caption("Space stuff.")
    pygame.mouse.set_visible(False)

    timer = time.Clock()
    print "Starting Game..."

    up = down = left = right = shoot = False        # the player has made no input
    
    #load background
    bg = Surface((32, 32))
    bg, bg_rect = load_image('bg02.jpg')
    #bg.convert()
    #bg.fill(Color("#1E1921"))
    
    #create objects and sprites, and groups for the sprites
    player = Player(400, 500)
    enemy = Enemy(400, 100)
    enemy2 = Enemy(200, 100)
    mouse = Mouse()

    platforms = []                      # not sure what I'm doing with this yet

    entities = pygame.sprite.Group(mouse, player, enemy, enemy2)
    enemies = pygame.sprite.Group(enemy, enemy2)
    bullets = pygame.sprite.Group()

    while 1:
        timer.tick(60)
        # have pygame listen for events
        for e in pygame.event.get():
            if e.type == QUIT: raise SystemExit, "QUIT"
            if e.type == KEYDOWN and e.key == K_ESCAPE: raise SystemExit, "ESCAPE"
            if e.type == KEYDOWN and e.key == K_UP:
                up = True
            if e.type == KEYDOWN and e.key == K_DOWN:
                down = True
            if e.type == KEYDOWN and e.key == K_LEFT:
                left = True
            if e.type == KEYDOWN and e.key == K_RIGHT:
                right = True
            if e.type == KEYDOWN and e.key == K_SPACE:                          # want to make this use mouse button down http://www.pygame.org/docs/ref/mouse.html#pygame.mouse.get_pressed
                bullet = Bullet(player.rect.centerx, player.rect.top - 32)
                bullets.add(bullet)
            if e.type == KEYUP and e.key == K_UP:
                up = False
            if e.type == KEYUP and e.key == K_DOWN:
                down = False
            if e.type == KEYUP and e.key == K_LEFT:
                left = False
            if e.type == KEYUP and e.key == K_RIGHT:
                right = False

        # draw background onto screen.
        #for y in range(20):
        #    for x in range(25):
        #        screen.blit(bg, (x * SCALE, y * SCALE))

        screen.blit(bg, (0,0))
        mouse.update()

        # update functions
        player.update(up, down, left, right, platforms, enemies)

        try:
            bullets.update(enemies)
        except:
            pass
        
        enemies.update()

        #draw everything
        entities.draw(screen)
        bullets.draw(screen)
        pygame.display.flip()


class Entity(pygame.sprite.Sprite):
    def __init__(self):
        pygame.sprite.Sprite.__init__(self)

    # didn't put the load image class here because bg uses it in main()

class Mouse(Entity):
    def __init__(self):
        Entity.__init__(self)
        self.image, self.rect = load_image('circles.png', -1)  

    def update(self):
        self.rect.center = pygame.mouse.get_pos()

class Player(Entity):
    def __init__(self, x, y):
        Entity.__init__(self)
        self.xvel = 0
        self.yvel = 0
        self.xaccel = 3
        self.yaccel = 2
        self.maxspeed = 10
        self.image, self.rect = load_image('player.png', -1)
        self.rect.center = (x, y)

    # uses screen_bind() to bind player and updates player position
    # checks if anyything in enemies has hit the player
    def update(self, up, down, left, right, platforms, enemies):
        hit = pygame.sprite.spritecollideany(self, enemies)
        if not hit:
            if up:
                if self.yvel > -self.maxspeed:
                    self.yvel -= self.yaccel
                else:
                    self.yvel = -self.maxspeed
            if down:
                if self.yvel  < self.maxspeed:
                    self.yvel += self.yaccel
                else:
                    self.yvel = self.maxspeed
            if left:
                if self.xvel > -self.maxspeed:
                    self.xvel -= self.xaccel
                else:
                    self.xvel = -self.maxspeed
            if right:
                if self.xvel < self.maxspeed:
                    self.xvel += self.xaccel
                else:
                    self.xvel = self.maxspeed
            

            if not(up or down):
                self.yvel = 0

            if not(left or right):
                self.xvel = 0

            self.rect.centerx += self.xvel
            self.rect.top += self.yvel

            self.screen_bind()
        else:
            hit.kill()
            self.kill()
            raise SystemExit, "YOU DIED."

            # explosion

    def screen_bind(self):
        if self.rect.centerx < 0:
            self.rect.centerx = 0
        elif self.rect.centerx > 800:
            self.rect.centerx = 800
        if self.rect.centery > 640:
            self.rect.centery = 640
        elif self.rect.centery < 0:
            self.rect.centery = 0

class Enemy(Entity):
    def __init__(self, x, y):
        Entity.__init__(self)
        self.xvel = 2
        self.yvel = 0
        self.xaccel = 1 
        self.yaccel = 1
        self.maxspeed = 6
        self.image, self.rect = load_image('enemy01.png', -1)
        self.rect.center = (x, y)

    # uses screen_bind() and updates enemy's position
    def update(self):
        self.rect.centerx = (self.rect.centerx + self.xvel)
        self.screen_bind()

    def screen_bind(self):
        if self.rect.centerx < 0:
            self.rect.centerx = 0
            self.xvel *= -1
        elif self.rect.centerx > 800:
            self.rect.centerx = 800
            self.xvel *= -1 


class Bullet(Entity):
    def __init__(self, x, y):
        Entity.__init__(self)
        self.xvel = 0
        self.yvel = -10
        self.shot = True
        self.image = Surface((10, 32))
        self.image.convert()
        self.image.fill(Color("#FFAA00"))
        self.rect = Rect(x, y, 10, 32)

    # checks if anything in enemies has hit bullet
    # checks if bullet is still on screen
    def update(self, enemies):
        hit = pygame.sprite.spritecollideany(self, enemies)
        if not hit:
            self.rect.left += self.xvel
            self.rect.top += self.yvel
            if self.rect.bottom <= 0:
                self.kill()
                # was used for testing self.kill() > print "Taget Destroyed"
                # was used for testing self.kill() > self.clear(screen, bg) 
            else:
                pass
        else:
            hit.kill()
            # explosion_images = load_sliced_sprites(16, 16, 'explosions-sprite.png')     # NEEDS MORE WORK
            self.kill()

# trying to make animated sprites
class AnimatedSprite(pygame.sprite.Sprite):
    def __init__(self, images, fps = 10):
        pygame.sprite.Sprite.__init__(self)
        self._images = images

        # Track the time we started, and the time between updates.
        # Then we can figure out when we have to switch the image.
        self._start = pygame.time.get_ticks()
        self._delay = 1000 / fps
        self._last_update = 0
        self._frame = 0

        # Call update to set our first image.
        self.update(pygame.time.get_ticks())

    def update(self, t):
        # Note that this doesn't work if it's been more that self._delay
        # time between calls to update(); we only update the image once
        # then, but it really should be updated twice.

        if t - self._last_update > self._delay:
            self._frame += 1
            if self._frame >= len(self._images): 
                self._frame = 0
            self.image = self._images[self._frame]
            self._last_update = t


if(__name__== "__main__"):
    main()