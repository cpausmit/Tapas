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
