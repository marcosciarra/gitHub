# creazione di un insieme
mySet = set()
mySet = set([10, 20, 30, 40])
mySet = {10, 20, 30, 40}

# creo insieme vuoto ed aggungo i valori
mySet = set()
mySet.add(10)
mySet.add(20)
mySet.add(30)

# Frozenset - Insieme immutabile
mySet = frozenset([10, 20, 30])
# aggiuungendo Python mi darebbe errore
mySet.add(40)

# per controllare se un valore appartiene si usa in
30 in mySet  # restituisce True

#Intersezione
mySet={10,20,30,40}
mySet2={30,40,50,60}
mySet & mySet2          #restituisce i valori presenti in entrambi, quindi {30,40}

#Unione
mySet | mySet2          #restituisce la somma logica {10,20,30,40,50,60}

# Differenza
mySet - mySet2          #sottrae dal primo elemento ciò che c'è nel secondo {10,20}

# Or esclusivo (XOR)
mySet ^ mySet2          #restituisce ciò che non c'è in entrambi quindi {10,50,20,60}

