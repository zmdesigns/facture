#include <string>

class JTServer {

public:
    JTServer();
    static bool makeRequest(std::string address, std::string file, std::string json_string);
};