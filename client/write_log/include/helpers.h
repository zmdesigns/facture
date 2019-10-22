#include <vector>

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