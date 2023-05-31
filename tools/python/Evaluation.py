class Eval:
    'An Eval class.'

    def __init__(self,number,lastName,firstName,description,email,evalO):
        self.number = number
        self.lastName = lastName
        self.firstName = firstName
        self.description = description
        self.email = email
        self.evalO = float(evalO)

    def update(self,email):
        self.email = email
        
    def show(self):
        print(" Number: %7s %s %s %s %s %3.1f"\
            %(self.number,self.lastName,self.firstName,self.description,self.email,self.evalO))

    def writeline(self):
        line = "%s,%s,%s,%s,%s,%f\n"\
            %(self.number,self.lastName,self.firstName,self.description,self.email,self.evalO)
        return line.encode('utf-8')

    def readline(self,line):
        f = line.split(",")
        if len(f) == 6:
            self.number = f[0]
            self.lastName = f[1]
            self.firstName = f[2]
            self.description = f[3]
            self.email = f[4]
            self.evalO = float(f[5])
            return 0
        else:
            return -1
