oldEmail = \'aml@space.mit.edu\'
newEmail = \'amlevine@mit.edu\'

# Teachers

select * from Teachers where Email = 'aml@space.mit.edu';
update Teachers set Email = 'amlevine@mit.edu' where Email = 'aml@space.mit.edu';

# Students

select * from Students where AdvisorEmail = 'aml@space.mit.edu';
update Students set AdvisorEmail = 'amlevine@mit.edu' where AdvisorEmail = 'aml@space.mit.edu';

select * from Students where SupervisorEmail = 'aml@space.mit.edu';
update Students set SupervisorEmail = 'amlevine@mit.edu' where SupervisorEmail = 'aml@space.mit.edu';

# Evaluations

select * from Evaluations where TeacherEmail = 'aml@space.mit.edu';
update Evaluations set TeacherEmail = 'amlevine@mit.edu' where TeacherEmail = 'aml@space.mit.edu';

# Assignments

select * from Assignments where Person = 'aml@space.mit.edu';
update Assignments set Person = 'amlevine@mit.edu' where Person = 'aml@space.mit.edu';
