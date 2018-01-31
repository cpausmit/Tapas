class Eval:
    'An Eval class.'

    def __init__(self,number,lastName,firstName,email,evalO):
        self.number = number
        self.lastName = lastName
        self.firstName = firstName
        self.email = email
        self.evalO = float(evalO)

    def update(self,email):
        self.email = email
        
    def show(self):
        print " Number: %7s %s %s %s %3.1f"\
            %(self.number,self.lastName,self.firstName,self.email,self.evalO)

    def writeline(self):
        return "%s,%s,%s,%s,%f\n"%(self.number,self.lastName,self.firstName,self.email,self.evalO)

    def readline(self,line):
        f = line.split(",")
        if len(f) == 5:
            self.number = f[0]
            self.lastName = f[1]
            self.firstName = f[2]
            self.email = f[3]
            self.evalO = float(f[4])
            return 0
        else:
            return -1
