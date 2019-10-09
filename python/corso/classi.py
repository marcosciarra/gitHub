# esistono 2 tipi di oggetti
# oggetti CLASSE
# oggetto ISTANZA

# baseClaas--> la nome-classe estende la baseClass
# class nomeClasse(baseClass):
#    statement


class myClass:
    pass


# attributi di classe
class myClass:
    myAttr = 10  # attributo di classe


myClass.myAttr  # stampa 10
myClass.attributo = 90  # attributo di istanza

class myClass:

    #costruttore
    #nel costruttore assegno all'attributo message il valore di message
    def __init__(self,message):
        self.message=message

    def myMethod(self):     #metodo della classe --> c'è sempre il primo parametro che è self che è l'istanza che stò invocando
        print(id(self))     #stampo l'dentificativo del metodo

    def myMethod2(self,parametro):
        print(parametro)

    #per dichiarare un metodo statico
    @staticmethod
    def somma(a,b):
        return (a+b)

m1 = myClass
#m1.myMethod()  -->  myClass.myMethod(m1)  sono la stessa cosa perchè chiamo il metodo di myClass per l'istanza m1
m1.myMethod2('python')





#ERIDITARIETA
class padre:
    pass

class figlio(padre):
    pass

#####################################################################à
#LA FUNZIONE SUPER

class padre:
    def __init__(self):
        pass

class figlio(padre):
    def __init__(self):
        super.__init__()
        pass
#Se creo una classe A e la eredito in B e B fa l'override di alnuni metodi da A,
#con Spuer posso accedere ai metodi della classe madre.
#Nell'esempio qui sopra nell'init di figlio chiamo l'init di padre

#In Python non esiste il vero e proprio Setter & Getter
#Per farlo dovremo fare:

class myClass():
    def __init__(self,attr):
        self.attributo=attr

    def getAttributo(self):
        return self.attributo

    def setAttributo(self,attr):
        self.attributo=attr


#Attributi fatti con due __ non sono visibili fuori della classe
class MyClass():
    def __init__(self,myAttr):
        self.__attributoPrivato__=myAttr    #Questo attributo è accessibile se faccio getter & setter ma non accessibile da fuori

