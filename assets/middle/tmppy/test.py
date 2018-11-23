def cube(num):
	return (num**3)
response = cube(int("5"))
correct = int("125")
if (response == correct):
	print('output is correct')
else:
	print(response)