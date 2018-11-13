def combineSorted(list1,list2):
	list3=list1+list2
	list3.sort()
	return list3
response = combineSorted(list([int ("-2"), int ("-1"), int ("5"), int ("6")]), list([int ("1"), int ("2"), int ("3"), int ("4")]))
correct = list([int ("-2"), int ("-1"), int ("1"), int ("2"), int ("3"), int ("4"), int ("5"), int ("6")])
if (response == correct):
	print('output is correct')
else:
	print(response)