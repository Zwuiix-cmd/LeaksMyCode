class Vector2
{
    x = 0;
    z = 0;

    constructor(x, z)
    {
        this.x = x;
        this.z = z;
    }

    getX()
    {
        return this.x;
    }

    getZ()
    {
        return this.z;
    }

    getFloorX()
    {
        return Math.floor(this.getX());
    }

    getFloorZ()
    {
        return Math.floor(this.getZ());
    }

    add(vector)
    {
        return new Vector2(this.getX() + vector.getX(), this.getZ() + vector.getZ());
    }

    addVector(vector)
    {
        return this.add(vector.getX(), vector.getZ());
    }

    subtract(x, z)
    {
        return this.add(-x, -z);
    }

    subtractVector(vector)
    {
        return this.subtract(-vector.getX(), -vector.getZ());
    }

    multiply(number)
    {
        return new Vector2(this.getX() * number, this.getZ() * number);
    }

    divide(number)
    {
        return new Vector2(this.getX() / number, this.getZ() / number);
    }

    ceil()
    {
        return new Vector2(Math.ceil(this.getX()), Math.ceil(this.getZ()));
    }

    floor()
    {
        return new Vector2(Math.floor(this.getX()), Math.floor(this.getZ()));
    }

    round()
    {
        return new Vector2(Math.round(this.getX()), Math.round(this.getZ()));
    }

    abs()
    {
        return new Vector2(Math.abs(this.getX()), Math.abs(this.getZ()));
    }

    asObject()
    {
        return { x: this.getX(), z: this.getZ() };
    }

    asFloorObject()
    {
        return {x: this.getFloorX(), z: this.getFloorZ()};
    }

    distance(vector)
    {
        return Math.sqrt(this.distanceSquared(vector));
    }

    distanceSquared(vector)
    {
        return ((this.getX() - vector.getX()) ** 2)  + ((this.getZ() - vector.getZ()) ** 2);
    }

    length()
    {
        return Math.sqrt(this.lengthSquared());
    }

    lengthSquared()
    {
        return (this.getX() ** 2) + (this.getZ() ** 2);
    }

    normalize(vector)
    {
        let len = this.lengthSquared(vector);
        if(len > 0){
            return this.divide(vector, Math.sqrt(len));
        }

        return new Vector2(0, 0);
    }
}
module.exports = Vector2;