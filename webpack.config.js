const { assertSupportedNodeVersion } = import('../src/Engine');
const { VueLoaderPlugin } = require('vue-loader')

module.exports = async () => {
    // @ts-ignore
    process.noDeprecation = true;

    assertSupportedNodeVersion();

    const mix = import('../src/Mix').primary;

    import(mix.paths.mix());

    await mix.installDependencies();
    await mix.init();

    return mix.build();
};
