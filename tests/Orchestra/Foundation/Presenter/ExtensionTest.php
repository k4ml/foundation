<?php namespace Orchestra\Foundation\Presenter\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Fluent;
use Orchestra\Foundation\Presenter\Extension;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Container;

        $this->app['orchestra.app'] = m::mock('\Orchestra\Foundation\Application[handles]');
        $this->app['translator'] = m::mock('\Illuminate\Translation\Translator[trans]');

        $this->app['orchestra.app']->shouldReceive('handles');
        $this->app['translator']->shouldReceive('trans');

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($this->app);
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($this->app);
        m::close();
    }

    /**
     * Test Orchestra\Foundation\Presenter\Extension::form()
     * method.
     *
     * @test
     */
    public function testFormMethod()
    {
        $app      = $this->app;
        $model    = new Fluent;
        $grid     = m::mock('\Orchestra\Html\Form\Grid')->shouldDeferMissing();
        $fieldset = m::mock('\Orchestra\Html\Form\Fieldset')->shouldDeferMissing();
        $control  = m::mock('\Orchestra\Html\Form\Control')->shouldDeferMissing();

        $stub = new Extension;

        $control->shouldReceive('label')->twice()->andReturnNull()
            ->shouldReceive('value')->once()->andReturnNull()
            ->shouldReceive('field')->once()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) {
                    $c();
                });
        $fieldset->shouldReceive('control')->twice()
            ->with('input:text', m::any(), m::type('Closure'))
            ->andReturnUsing(function ($t, $n, $c) use ($control) {
                $c($control);
            });
        $grid->shouldReceive('setup')->once()
                ->with($stub, 'orchestra::extensions/configure/foo.bar', $model)->andReturnNull()
            ->shouldReceive('fieldset')->once()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) use ($fieldset) {
                    $c($fieldset);
                });

        $app['orchestra.extension'] = m::mock('\Orchestra\Extension\Factory[option]');
        $app['orchestra.form'] = m::mock('\Orchestra\Html\Form\Environment')->shouldDeferMissing();
        $app['html'] = m::mock('\Orchestra\Html\HtmlBuilder[link]');

        $app['orchestra.extension']->shouldReceive('option')->once()->with('foo/bar', 'handles')->andReturn('foo');
        $app['orchestra.form']->shouldReceive('of')->once()
                ->with('orchestra.extension: foo/bar', m::type('Closure'))
                ->andReturnUsing(function ($t, $c) use ($grid) {
                    $c($grid);
                    return 'foo';
                });
        $app['html']->shouldReceive('link')->once()
                ->with(handles("orchestra/foundation::extensions/update/foo.bar"), m::any(), m::any())
                ->andReturn('foo');

        $this->assertEquals('foo', $stub->configure($model, 'foo/bar'));
    }
}
