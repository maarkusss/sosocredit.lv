<div
    class="fixed inset-0 z-[1049] bg-gray-900/50"
    x-data="{
        visible: false,
        link: null,
        fields: {},
        values: {},
        errors: {},
        locale: 'lv',
        labels: {{ json_encode(__('fields')) }},
        validate() {
            this.errors = {};
                for (const [name, field] of Object.entries(this.fields)) {
                    if (field.enabled === false) continue;

                    if (field.type === 'checkbox') {
                        if (field.required && !this.values[name]) this.errors[name] = 'required';
                        continue;
                    }

                    const value = String(this.values[name] ?? '').trim();
                    if (value === '') {
                        if (field.required) this.errors[name] = 'required';
                        continue;
                    }

                    if (field.validation) {
                        try {
                            if (!new RegExp(field.validation).test(value)) this.errors[name] = 'format';
                        } catch (e) {
                            console.warn('Invalid validation pattern for', name, e);
                        }
                    }
                }
            return Object.keys(this.errors).length === 0;
        }
    }"
    x-show="visible"
    x-cloak
    x-on:open-additional-fields.window="
        visible = true;
        link = $event.detail.link;
        fields = $event.detail.fields;
        locale = $event.detail.locale;
        values = Object.fromEntries(
            Object.keys(fields).map(name => [name, fields[name].type === 'checkbox' ? false : ''])
        );
        errors = {};
    "
    x-on:keydown.escape.window="visible = false"
>
    <div
        id="additional-required-fields-modal"
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[1050] w-[calc(100vw-2rem)] max-w-md max-h-[90vh] overflow-y-auto rounded-lg border border-gray-200 bg-white p-6 shadow-lg"
    >

        <button type="button"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-2xl leading-none"
                x-on:click="visible = false">
            &times;
        </button>

        <div class="mb-4 text-center">
            <p class="pb-2 text-neutral-900 text-lg font-bold text-left">
                @lang('ADF header')
            </p>
            <p class="text-sm text-gray-900 text-left">
                @lang('ADF description header')
            </p>
        </div>

        <form method="POST"
              x-bind:action="link"
              target="_blank"
              novalidate
              x-on:submit="
          if (!validate()) { $event.preventDefault(); return; }
          visible = false;
      ">
            @csrf
            <div class="flex flex-col justify-between h-full">
                <template x-for="name in Object.keys(fields)" x-bind:key="name">
                    <div class="inline-block text-sm text-gray-500 mb-2">

                        {{--                    <label x-show="fields[name].type !== 'checkbox'"--}}
                        {{--                           class="block mb-1 text-sm font-bold text-neutral-900"--}}
                        {{--                           x-bind:for="'field_' + name">--}}
                        {{--                        <span x-text="fields[name].translations.label[locale] ?? name"></span>--}}
                        {{--                        <span x-show="fields[name].required" class="text-red-500">*</span>--}}
                        {{--                    </label>--}}

                        {{-- select --}}
                        <template x-if="fields[name].type === 'select'">
                            <select class="w-full px-4 py-3 bg-white rounded focus:border-loan-dark focus:ring-loan-dark"
                                    x-bind:class="errors[name] ? 'border-red-500' : 'border-gray-300'"
                                    x-bind:id="'field_' + name"
                                    x-bind:name="name"
                                    x-model="values[name]"
                                    x-on:change="delete errors[name]">
                                <option value="" disabled
                                        x-text="fields[name].translations.placeholder?.[locale] ?? ''"></option>
                                <template x-for="option in Object.keys(fields[name].options ?? {})" x-bind:key="option">
                                    <option x-bind:value="option"
                                            x-text="fields[name].options[option][locale] ?? option"></option>
                                </template>
                            </select>
                        </template>

                        {{-- checkbox --}}
                        <template x-if="fields[name].type === 'checkbox'">
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-neutral-900 cursor-pointer">
                                <input type="checkbox"
                                       class="rounded text-loan-dark focus:ring-loan-dark"
                                       x-bind:class="errors[name] ? 'border-red-500' : 'border-gray-300'"
                                       x-bind:name="name"
                                       x-model="values[name]"
                                       x-on:change="delete errors[name]">
                                <span x-text="fields[name].translations.label[locale] ?? name"></span>
                                <span x-show="fields[name].required" class="text-red-500">*</span>
                            </label>
                        </template>

                        {{-- text, number, date --}}
                        <template x-if="!['select', 'checkbox'].includes(fields[name].type)">
                            <input class="w-full px-4 py-3 bg-white rounded focus:border-loan-dark focus:ring-loan-dark"
                                   x-bind:class="errors[name] ? 'border-red-500' : 'border-gray-300'"
                                   x-bind:id="'field_' + name"
                                   x-bind:name="name"
                                   x-bind:type="fields[name].type"
                                   x-bind:maxlength="fields[name].type === 'text' ? fields[name].length : null"
                                   x-bind:placeholder="(fields[name].translations.placeholder?.[locale] ?? '') + (fields[name].required ? '*' : '')"
                                   x-model="values[name]"
                                   x-on:input="delete errors[name]">
                        </template>

                        <p x-show="errors[name] === 'required'" class="mt-1 text-sm text-red-600">
                            @lang('This field is required')
                        </p>

                        <p x-show="errors[name] === 'format'" class="mt-1 text-sm text-red-600">
                            @lang('Invalid document number format')
                        </p>
                    </div>
                </template>
                <div>
                    <p class="text-xs leading-tight text-gray-500">
                        @lang('ADF description')
                    </p>
                </div>
            </div>

            <div class="justify-items-stretch w-full">
                <button type="submit"
                        class="mt-3 h-11 w-full flex items-center justify-center rounded border border-green-600 px-6 text-base font-medium text-white button_primary">
                    @lang('Take offer')
                </button>
            </div>
        </form>
    </div>
</div>
