# sono delle liste chiave (Key) valore (Value)
# type = dict

# crea un dizionario vuoto
myDict = {}

myDict = {
    "primo": 10,
    "secondo": 20,
    "terzo": 30
}

# se la chiave esiste, cambio il valore
# se la chiave non esiste, aggiunge una nuova chiave
myDict["quarto"] = 40

# serve per eliminare Key e Value indicato
del myDict["secondo"]

# se voglio svuotare il dizionario
myDict.clear()
# oppure
myDict = {}

# operatore in, serve per controllare se la chiave indicata esiste
"terzo" in myDict  # restituisce True o False

# per copiare un dizionario senz ache faccia riferimento allo stesso puntatore
myDict2 = myDict.copy()

# restituisce una lista di tuple (key,Value)
d1 = {
    10: 'a',
    20: 'b'
}
l1 = myDict.items()
# restituisce  [(10,'a'),(20,'b')]

# Posso creare un dizionario da una lista di tuple
newDict = dict(l1)
