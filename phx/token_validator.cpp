
#include "headers/token_validator.hpp"
#include "headers/token_validator_class.hpp"

Php::Value
validate_token() {
	token_validator *validator = new token_validator();
	int r = validator->validate();
	free(validator);
	validator = nullptr;
	return r;
}
