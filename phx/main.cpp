
extern "C" {
	PHPCPP_EXPORT void *get_module() {
		static Php::Extension extension("apismesummit_ext1");
		extension.add<>()
	}
}
