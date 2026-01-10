export declare function createI18n<
    Schema extends object = DefaultLocaleMessageSchema,
    Locales extends string | object = "de-DE",
    Legacy extends boolean = true,
    Options extends I18nOptions<SchemaParams<Schema, VueMessageType>, LocaleParams<Locales>> = I18nOptions<
        SchemaParams<Schema, VueMessageType>,
        LocaleParams<Locales>
    >,
    Messages extends Record<string, unknown> = NonNullable<Options["messages"]> extends Record<string, unknown>
        ? NonNullable<Options["messages"]>
        : {},
    DateTimeFormats extends Record<string, unknown> = NonNullable<Options["datetimeFormats"]> extends Record<
        string,
        unknown
    >
        ? NonNullable<Options["datetimeFormats"]>
        : {},
    NumberFormats extends Record<string, unknown> = NonNullable<Options["numberFormats"]> extends Record<
        string,
        unknown
    >
        ? NonNullable<Options["numberFormats"]>
        : {},
    OptionLocale = Options["locale"] extends string ? Options["locale"] : Locale
>(
    options: Options
): (typeof options)["legacy"] extends true
    ? I18n<Messages, DateTimeFormats, NumberFormats, OptionLocale, true>
    : (typeof options)["legacy"] extends false
      ? I18n<Messages, DateTimeFormats, NumberFormats, OptionLocale, false>
      : I18n<Messages, DateTimeFormats, NumberFormats, OptionLocale, Legacy>;
