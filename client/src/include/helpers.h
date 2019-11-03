#ifndef __HELPERS_H__
#define __HELPERS_H__

#include <vector>

//split a string into a vector where each element is the content between char s and and e
//ie- jobs:{job1,product:2,product:3},{job2,product:5} 
//  would return:
//vector<string> with 2 elements: job1,product:2,product:3, job2,product:5
std::vector<std::string>seperate(std::string str,char s, char e) {
    std::vector<std::string> v;
    bool started = false;
    std::string buffer = "";

    for(std::string::iterator it=str.begin(); it != str.end(); ++it) {
        char c = *it;
        if (!started) {
            if (c == s) {
                started = true;
            }
        }
        if (started) {
            buffer += c;

            if (c == e) {
                v.push_back(buffer);
                buffer = "";
                started = false;
            }
        }
    }
    return v;
}

//returns symbol for char representation of number passed
//or number for symbol passed
//ie- 1=!, 7=&, ^=6, )=0
char toggle_number_symbol(char num) {
    switch(num) {
        case '1':
            return '!';
        case '2':
            return '@';
        case '3':
            return '#';
        case '4':
            return '$';
        case '5':
            return '%';
        case '6':
            return '^';
        case '7':
            return '&';
        case '8':
            return '*';
        case '9':
            return '(';
        case '0':
            return ')';

        case '!':
            return '1';
        case '@':
            return '2';
        case '#':
            return '3';
        case '$':
            return '4';
        case '%':
            return '5';
        case '^':
            return '6';
        case '&':
            return '7';
        case '*':
            return '8';
        case '(':
            return '9';
        case ')':
            return '0';
    }
    return num;
}

//if cap=true: capitalize letter/turn number->symbol
//if cap=false: lowercase letter/turn symbol->number
char toggle_caps(char c, bool cap) {
    if (isalpha(c)) {
        if (cap)
            return toupper(c);
        else
            return tolower(c);
    }
    else {
        return toggle_number_symbol(c);
    }
}

#endif