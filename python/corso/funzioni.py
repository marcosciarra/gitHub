# Una funzione è un oggetto "chiamabile" --> definizione per Python --> Collable Object

# def    --> definisce la funzione
def nomeFunzione(parametro1, parametro2):
    parametro1 = 1
    parametro2 = 2
    print
    parametro1 * parametro2


# parametri fissi
def func1(parametro1, parametro2)


# parametri keeyword
def func1(parametro1, parametro2=30)


# parametri opzionali
def func1(parametro1, parametro2=30)


# parametri *args --> riceverà una tupla di argomenti
def func1(*args)  # --> func(1,2,3,4)   --> python ricevera una tupla composta da (1,2,3,4)


def func1(a, b, *args)  # posso usare x parametri posizionali + una tupla di paramentri


# keyword Aìargs --> **kwargs
# crea un dizionario in chiave valore di parametri
def func1(**kwargs):


func1('a' = 1, 'b' = 2, 'c' = 3)

# return
def sum(a, b):
    return a + b

########################################################################################################################

# Esempio di funzione che controlla i valori diversi tra due liste (secondo paramentro opzionale)
def listadif(l1, l2=[1, 2, 3, 4, 5]):
    l = []
    for x in l1:
        if not x in l2:
            l.append(x)
    return l

########################################################################################################################
#Funzioni come oggetto

# in Python è possibile nidificare le funzioni
def primaFun(x, y):
    def secondaFun(a, b):
        return a + b
    print(secondaFun(x, y))

########################################################################################################################

#mettendo in una funzione senza paramentri un'altra funzione posso associare la funzione ad una variabile per poi
#richimare la funzione dalla variabile
def fuori():
    def dentro(a,b):
        print(a+b)
    return dentro

#f punta alla funzione dentro
f=fuori()
f(5,6)
#viene stampato 11

########################################################################################################################
#creo una funzione Sum e una myFunc la cui come 1° parametro gli passo una funzione
def sum(a,b):
    print(a+b)

def myFunc(f,x,y):
    f(x,y)

myFunc(sum,10,5)
#stampa 15