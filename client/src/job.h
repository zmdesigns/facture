#ifndef __JOB_H__
#define __JOB_H__

#include <string>


class Job {

public:
    Job(int job, int product);
    std::string job_string();

    int job_id;
    int product_id;

};

#endif