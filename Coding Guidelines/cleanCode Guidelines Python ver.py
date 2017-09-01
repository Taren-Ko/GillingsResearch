#Keep comments on the same indent as the module it belongs to

# starting parameters should be single-spaced, functions should have spaces before and after themselves
# These rules also apply for parameters and functions inside of functions
# Give comments detailing a module's purpose above the module and give your name as the creator
# Use tabs in code for hierarchy, not spaces

import sys

def superScript(): #use superScript for function names
    something = 0
    somethingElse = 'blah'

    while(something != 10):
        print('This is printed 10 times.')
        something++

    if(somethingElse == 1):
        print('Something isnt 1')

    #TODO: use todo lines to specify areas where work is needed

def anotherFunc(var):
    if(var): #code should start on line right after def declaration
        return 0
