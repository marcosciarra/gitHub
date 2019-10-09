id(20)  # restituisce i punto di memoria del valore della variabile
type(20)  # Restituisce il tipo di variabile

# UPPER su stringhe
c = 'python'
x.upper()  # stampa PYTHON
# oppure
'python'.upper()  # stampa PYTHON

##BASIC DATA TYPE##
#   None --> serve per indicare l'assenza di un valore
None

#   Integer             Int         Numeri interi
#   Floathing-Point     Float       Numeri con decimali
#   Boolean             Bool        True o False

0b0101010101  # numero binario
0o17642  # numero ottale
0x1F5A  # numero esadecimale

##############################################  IF  ####################################################################

if x < 10:
    print ('X è minore di 10')
elif x > 20:
    print ('X è maggiore di 20')
else:
    print ('X è minore o uguale di 20 e maggiore o uguale di 10')

##############################################  WHILE  #################################################################
x = 0
while x < 10:
    x += 1
    if x == 5:  # salto il ciclo 5 quindi non stamperò 5
        continue
    print(x)
    if x == 9:  # blocco ciclo a 9 ed esco dal while
        break
else:  # facoltativa
    print ('Fine del giro')  # facoltativa

##############################################  FOR  ###################################################################
# Associo ad 'i' i valori della lista (come il foreasch)
myList = [1, 2, 3, 4]
for i in myList:
    print (i)
else:  # facoltativa
    print ('fine')  # facoltativa

myString = 'python'
for i in myString:
    print (i)

# itero le chiavi del dizionario
myDict = {'a': 1, 'b': 2, 'c': 3}
for i in myDict:
    print (i)  # 'a' - 'b' - 'c'

# itero i valori del dizionario
for i in myDict.values():
    print (i)  # 1 - 2 - 3

# itero le chivi,valori del dizionario
for i in myDict.items():
    print (i)  # ('a',1) - ('b',2) - ('c',3)
# anche nel ciclo for in è possibile usare il break x uscire e continue per saltare un giro

##############################################  RANGE  #################################################################
# Range è una funzione che crea un oggetto iterabile
range(start, stop, step)
# stop obbligatorio
# start di default è 0
# step di default è 1
for i in range(10, 16, 2):
    print (i)  # stampa 10 - 12 - 14

##############################################  LOOP  ##################################################################

range(start, stop, step)
# stop obbligatorio
# start di default è 0
# step di default è 1
for i in range(10, 16, 2):
    print (i)  # stampa 10 - 12 - 14

##############################################  LIST COMPREHENSION  ######################################################

#   [expression for item in iterable if condition]
# expression    --> espressione sul valore di item
# for in        --> ciclo for
# item          --> valore che viene assegnato dal ciclo condizionato da condition
# iterable      --> lista o oggetto da iterare
# condition     --> condizione per verificare il valore iterato da assegnare ad item
num = [1, 2, 3, 4, 5, 6, 7, 8, 9]
newList = [n * n for n in num if n % 2 == 1]
newList  # [1 ,9 ,25 ,49 ,81]


