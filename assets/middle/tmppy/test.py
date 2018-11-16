def mathOperations(str1,str2):
	if(str1 in str2):
		return True
	else:
		return False
response = mathOperations(str("%"), int("13"), int("6"))
correct = int("1")
if (response == correct):
	print('output is correct')
else:
	print(response)