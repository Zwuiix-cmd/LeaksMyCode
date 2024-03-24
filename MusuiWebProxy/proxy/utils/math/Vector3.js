class Vector3
{
    x = 0;
    y = 0;
    z = 0;

    constructor(x, y, z)
    {
        this.x = x;
        this.y = y;
        this.z = z;
    }

    getX()
    {
        return this.x;
    }

    getY()
    {
        return this.y;
    }

    getZ()
    {
        return this.z;
    }

    getFloorX()
    {
        return Math.floor(this.getX());
    }

    getFloorY()
    {
        return Math.floor(this.getY());
    }

    getFloorZ()
    {
        return Math.floor(this.getZ());
    }

    add(x, y, z)
    {
        return new Vector3(this.getX() + x, this.getY() + y, this.getZ() + z);
    }

    addVector(vector)
    {
        return this.add(vector.getX(), vector.getY(), vector.getZ());
    }

    subtract(x, y, z)
    {
        return this.add(-x, -y, -z);
    }

    subtractVector(vector)
    {
        return this.subtract(-vector.getX(), -vector.getY(), -vector.getZ());
    }

    multiply(number)
    {
        return new Vector3(this.getX() * number, this.getY() * number, this.getZ() * number);
    }

    divide(integer)
    {
        return new Vector3(this.getX() / integer, this.getY() / integer, this.getZ() / integer);
    }

    ceil()
    {
        return new Vector3(Math.ceil(this.getX()), Math.ceil(this.getY()), Math.ceil(this.getZ()));
    }

    floor()
    {
        return new Vector3(Math.floor(this.getX()), Math.floor(this.getY()), Math.floor(this.getZ()));
    }

    round()
    {
        return new Vector3(Math.round(this.getX()), Math.round(this.getY()), Math.round(this.getZ()));
    }

    abs()
    {
        return new Vector3(Math.abs(this.getX()), Math.abs(this.getY()), Math.abs(this.getZ()));
    }

    asObject()
    {
        return { x: this.getX(), y: this.getY(), z: this.getZ() };
    }

    asFloorObject()
    {
        return {x: this.getFloorX(), y: this.getFloorY(), z: this.getFloorZ()};
    }

    asString()
    {
        return `X: ${this.getFloorX()} Y: ${this.getFloorY()} Z: ${this.getFloorZ()}`;
    }

    distance(vector)
    {
        return Math.sqrt(this.distanceSquared(vector));
    }

    distanceSquared(vector)
    {
        return ((this.getX() - vector.getX()) ** 2)  + ((this.getY() - vector.getY()) ** 2) + ((this.getZ() - vector.getZ()) ** 2);
    }

    length()
    {
        return Math.sqrt(this.lengthSquared());
    }

    lengthSquared()
    {
        return (this.getX() * this.getX()) + (this.getY() * this.getY()) + (this.getZ() * this.getZ());
    }

    normalize(vector)
    {
        let len = this.lengthSquared(vector);
        if(len > 0){
            return vector.divide(Math.sqrt(len));
        }

        return new Vector3(0, 0, 0);
    }

    equal(vector)
    {
        return this.getFloorX() === vector.getFloorX() && this.getFloorY() === vector.getFloorY() && this.getFloorZ() === vector.getFloorZ();
    }
}
module.exports = {
    Vector3,
    fromObject(object) {
        if(isNaN(object.x) || isNaN(object.y) || isNaN(object.z)) {
            throw Error("A Vector3 cannot contain invalid number(s) as parameters");
        }
        return new Vector3(object.x, object.y, object.z);
    }
};