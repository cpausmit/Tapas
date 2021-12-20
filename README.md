# Tapas

Tapas is the Teaching Assistant in Physics Assignment System

# Planning phase

Add a new semester

  https://tapas.mit.edu/addSemester

Define the course resources (usually the physics department)

  https://tapas.mit.edu/planCourseResources

## Planning and Active Tables

There are a number of tables relevant for the active term and for planning. Use the following tool to select those.

  https://tapas.mit.edu/selectTerm

The semesters are denoted as $term. For the *active* term we have

  Assignments ($term)
  Evaluations ($term)
  Preferences ($term)
  Tas ($term)
  
For the *planning* term we have

  Assignments ($term)
  Evaluations ($term)
  Tas ($term)


## Preparing the available slots

Assuming the $term variable the generateSlots.py tool will generate the available slots later to be used in the selection of preferences.

  generateSlots.py $term d

To make sure that the slots will be available for the preferences the slots need to get a dummy assignment. The 'EMPTY' string is the preferred placeholder.

  addAssignments.py $term exec remove

Double check that selections are working at:

  https://tapas.mit.edu/select

Now we are ready to send the email.

