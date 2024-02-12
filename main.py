database = [['Armen','4455',0],['Davit','4002',0],['Artak','1111',0],['Joe','0000',0]]

while True:
    entered_password = input('Enter your password =>> ')
    for i in range(0, len(database)):
        if database[i][1] == entered_password:
            user_id = i
            print('-'*25)
            print(f'Welcome {database[user_id][0]} !')
            print('-' * 25)
            print('Check balance - B')
            print('Cash In - (+)')
            print('Cash Out - (-)')
            print('Transaction - T')
            print('Exit - E')
            print('-'*25)
            while True:
                command = input('Enter command =>> ')
                if command == 'B':
                    print(f'Balance: {database[user_id][2]} $')
                elif command == '+':
                    sum = int(input('Enter sum =>> '))
                    if sum <= 0:
                        print('Invalid sum, try again')
                    else:
                        database[user_id][2] += sum
                elif command == '-':
                    sum = int(input('Enter sum =>> '))
                    if sum <= 0 or sum > database[user_id][2]:
                        print('Invalid sum, try again')
                    else:
                        database[user_id][2] -= sum
                elif command == 'T':
                    entered_id = int(input('Enter id =>> '))
                    if entered_id == user_id or entered_id >= len(database):
                        print('Error, invalid id !')
                    else:
                        sum = int(input('Enter sum =>> '))
                        if sum <= 0 or sum > database[user_id][2]:
                            print('Invalid sum, try again')
                        else:
                            database[user_id][2] -= sum
                            database[entered_id][2] += sum
                elif command == 'E':
                    break
            break
        elif i == len(database) - 1:
            print('Invalid password !')
