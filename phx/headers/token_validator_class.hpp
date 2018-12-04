
#ifndef __TOKEN_VALIDATOR_CLASS_HPP
#define __TOKEN_VALIDATOR_CLASS_HPP 1

#include <phpcpp.h>

class token_validator
{
public:
	token_validator(std::string token);
	~token_validator();
	int validate();
private:
	int validate_header();	
	int validate_token();

	std::string token;
};

#endif
