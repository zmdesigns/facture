#include "job.h"

Job::Job(int job, int product) : job_id(job), product_id(product) {
    
}

std::string Job::job_string() {
    std::string job_str = "Job:" +
                std::to_string(job_id) +
                " Product:" +
                std::to_string(product_id);

    return job_str;
}